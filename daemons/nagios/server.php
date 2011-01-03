<?php
require_once(__DIR__ . '/../bootstrap.php');
TBase::$allowEnumConversion = true; 

/**
* Nagios configuration handler
*/
class NagiosHandler implements \Thrift\Nagios\NagiosIf {
	private $fp;
	
	// Write section to config file
	private function writeSection($name, $params) {
		fwrite($this->fp, "define $name {\n");
		foreach($params as $k => $v) {
			if(isset($v)) fwrite($this->fp, "\t$k\t\t$v\n");
		}
		fwrite($this->fp, "}\n\n");
	}

	public function updateConfiguration($conf) {
		// Prepare config file
		if(!$this->fp = fopen('/etc/nagios3/conf.d/servisweb.cfg', 'w')) throw new TException("Unable to open config file");
		
		$groups = array();
		$code = array(); // Parts of code
		
		// Localhost
		$groups['ssh-servers'][] = 'localhost';
		$groups['http-servers'][] = 'localhost';
		$groups['ping-servers'][] = 'localhost';
		
		foreach($conf->hosts as $host) {
			// Host section
			$this->writeSection('host', array(
				'use'		=> $host->template,
				'host_name'	=> $host->hostName,
				'parents'	=> $host->parents ? implode(',', $host->parents) : null,
				'alias'		=> $host->hostAlias ?: $host->hostName,
				'address'	=> $host->ip,
				'contact_groups'=> $host->contactGroup ?: 'noone',
			));
			
			// Extended info
			$this->writeSection('hostextinfo', array(
				'host_name'	=> $host->hostName,
				'statusmap_image'=> $host->image,
				'2d_coords'	=> $host->coords ? "{$host->coords->posX},{$host->coords->posY}" : null,
				'action_url'	=> $host->url,
			));
			
			// Add to group
			if(is_array($host->groups)) foreach($host->groups as $g) $groups[$g][] = $host->hostName;
			if(is_array($host->services)) foreach($host->services as $g) {
				$service = $g; //@\Thrift\Nagios\CheckService::$__names[$g];
				if($service) $groups["$service-servers"][] = $host->hostName;
			}
		}
		
		// Write groups
		foreach($groups as $g => $hosts) {
			$this->writeSection('hostgroup', array(
				'hostgroup_name'	=> $g,
				'alias'			=> @$conf->groupAliases[$g] ?: $g,
				'members'		=> implode(',', $hosts),
			));
		}
		
		// Close config file
		fclose($this->fp);
		
		// Reload config
		$this->reloadConfig();
	}
	
	/**
	* Reload config
	*/
	public function reloadConfig() {
		$pid = trim(@file_get_contents('/var/run/nagios3/nagios3.pid'));
		if($pid) exec("kill -s HUP $pid", $_, $r);
		else $r = 1;
		
		// Try restarting it
		if($r != 0) $this->restart();
	}
	
	public function start() {
		exec("/etc/init.d/nagios3 start");
	}
	
	public function stop() {
		exec("/etc/init.d/nagios3 stop");
	}
	
	public function restart() {
		exec("/etc/init.d/nagios3 restart");
	}
	
	/**
	* Get statistics
	*/
	public function getStatistics() {
		return `nagios3stats`;
	}
}



// Create handler
$handler = new NagiosHandler();

// Processor
$processor = new \Thrift\Nagios\NagiosProcessor($handler);

// Run server loop
runUnixSocketServer('nagios', $processor);

