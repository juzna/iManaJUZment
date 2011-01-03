<?php
define('RRD_DIR', APP_DIR . '/storage/rrd/');
define('RRD_EXEC', '/usr/bin/rrdtool');

class RRDTool {
	// Fields for given types of rrd files
	static $fields = array(
		'traffic'	=> array('down', 'up'),
		'interface'	=> array('in', 'inerr', 'out', 'outerr', 'queue'),
		'regtable'	=> array('strength', 'tx-bytes', 'rx-bytes', 'tx-packets', 'rx-packets', 'tx-rate', 'rx-rate'),
		'resource'	=> array('size', 'used', 'failures'),
		'uptime'	=> array('cpu', 'uptime', 'snmp_load'),
		'ping'		=> array('ms', 'loss'),
	);
	
	/**
	* Execute command
	*/
	private static function exec($cmd, $stdIn = null) {
		// Pipes pro procesy
		$descriptorspec = array(
			0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
			1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
			2 => array("pipe", "w"),  // stderr
		);
		
		// Spustime
		$process = proc_open($cmd, $descriptorspec, $pipes);
		if(!is_resource($process)) return false;
		
		// Dump input
		if($stdIn) fwrite($pipes[0], $stdIn);
		fclose($pipes[0]);
		
		// Cekame na ukonceni
		do {
			$stav = proc_get_status($process);
			if(!$stav['running']) break;
			
			usleep(10 * 1000); // 10ms
		} while($stav);
		
		// Nacteme vysledek
		$err  = stream_get_contents($pipes[2]);
		$data = stream_get_contents($pipes[1]);
	
		$retValue = proc_close($process);
		
		if($data && defined(LOG_NORMAL)) file_put_contents(LOG_NORMAL, date('Y-m-d H:i:s') . "\n$data\n", FILE_APPEND);
		if($err && defined(LOG_ERROR)) file_put_contents(LOG_ERROR, date('Y-m-d H:i:s') . "\n$err\n", FILE_APPEND);
		
		return $retValue == 0;
	}
	
	private static function sanitizeFileName($s) {
		return preg_replace('/[^a-zA-Z0-9.-_]/', '_', $s);
	}
	
	/**
	* Get file name for rrd database
	* @param string $type Type of table
	* @param array $params Parameters
	*/
	public static function getFileName($type, $params) {
		switch($type) {
			case 'traffic':		return "traffic/{$params['ip']}-{$params['ap']}";
			case 'interface':	return "interface/{$params['ap']}-{$params['if']}";
			case 'regtable':	return "regtable/{$params['mac']}-{$params['ap']}";
			case 'resource':	return "resource/{$params['ap']}-{$params['typ']}";
			case 'uptime':		return "cpu/{$params['ap']}";
			case 'ping':		return "ping/{$params['ip']}";
			default:		return false;
		}
	}
	
	/**
	* Save data to RRD database
	*/
	public static function save($type, $params, $time = null) {
		if(!$fileName = self::sanitizeFileName(self::getFileName($type, $params))) return false;
		$path = RRD_DIR . '/' . $fileName . '.rrd';
		
		// Create new DB
		if(!file_exists($path)) self::createDB($type, $path);
		
		// Prepare command
		$row = array($time ?: time());
		foreach(self::$fields[$type] as $field) $row[] = $params[$field];
		$cmd = RRD_EXEC . " update '$path' " . implode(':', $row);
		
		// Add it to command
		self::exec($cmd);
		
		return true;
	}
	
	/**
	* Create new database
	*/
	static function createDB($type, $path) {
		// Create dir
		if(!file_exists($dir = dirname($path))) mkdir2($dir);
		
		// Get definition
		$step = 300;
		$def = self::getDBDefinition($type, $step);
		
		// Prepare command
		$startTime = time() - 600; // T - 10minutes
		$cmd = RRD_EXEC . " create '$path' --step '$step' --start '$startTime' $def ";
		
		// Add agregation
		$cmd .= "RRA:AVERAGE:0.5:2:1440 ";
		$cmd .= "RRA:AVERAGE:0.5:60:720 ";
		$cmd .= "RRA:AVERAGE:0.5:1440:366 ";
	
		$cmd .= "RRA:MAX:0.5:1:1440 ";
		$cmd .= "RRA:MAX:0.5:60:720 ";
		$cmd .= "RRA:MAX:0.5:1440:366 ";
		
		self::exec($cmd);
	}
	
	/**
	* Get definition of database
	*/
	private static function getDBDefinition($type, &$step = null) {
		$cmd = '';
		
		switch($type) {
			// Traffic
			case 'traffic':
				$cmd .= "DS:down:COUNTER:600:U:U ";
				$cmd .= "DS:up:COUNTER:600:U:U ";
				break;
				
			// Interface
			case 'interface':
				$cmd .= "DS:in:COUNTER:600:U:U ";
				$cmd .= "DS:in-error:COUNTER:600:U:U ";
				$cmd .= "DS:out:COUNTER:600:U:U ";
				$cmd .= "DS:out-error:COUNTER:600:U:U ";
				$cmd .= "DS:out-queue:GAUGE:600:U:U ";
				break;
				
			// Regtable
			case 'regtable':
				$cmd .= "DS:strength:GAUGE:600:U:U ";
				$cmd .= "DS:tx-bytes:COUNTER:600:U:U ";
				$cmd .= "DS:rx-bytes:COUNTER:600:U:U ";
				$cmd .= "DS:tx-packets:COUNTER:600:U:U ";
				$cmd .= "DS:rx-packets:COUNTER:600:U:U ";
				$cmd .= "DS:tx-rate:GAUGE:600:U:U ";
				$cmd .= "DS:rx-rate:GAUGE:600:U:U ";
				break;
			
			// Resource
			case 'resource':
				$cmd .= "DS:size:GAUGE:600:U:U ";
				$cmd .= "DS:used:GAUGE:600:U:U ";
				$cmd .= "DS:failures:GAUGE:600:U:U ";
				break;
			
			// Uptime + CPU
			case 'uptime':
				$cmd .= "DS:cpu:GAUGE:600:U:U ";
				$cmd .= "DS:uptime:GAUGE:600:U:U ";
				$cmd .= "DS:snmp-load:GAUGE:600:U:U ";
				break;
				
			// Ping
			case 'ping':
				$step = 10;
				$cmd .= "DS:ping:GAUGE:30:U:U ";
				$cmd .= "DS:loss:GAUGE:30:U:U ";
				break;
		}
		
		return $cmd;
	}
}

