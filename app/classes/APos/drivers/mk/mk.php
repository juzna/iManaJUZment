<?php
/**
* Connection class for Mikrotik
*/
namespace APos\Handlers;

// Load Thrift
require_once __DIR__ . '/../../../../3rdParty/thrift/bootstrap.php';

// Old communication classes
require_once __DIR__ . '/fce.inc.php';
require_once __DIR__ . '/mikrotikos.inc.php';
require_once __DIR__ . '/mikrotik.inc.php';
require_once __DIR__ . '/routeros.inc.php';

// Services
require_once __DIR__ . '/../../base.php';
require_once __DIR__ . '/services/base.php';



class MkHandler implements \Thrift\APos\MkIf {
	private $ap;	// Access point info
	private $mk;	// mikrotikos
	private $shell;	// SSH shell
	private $api;	// Mikrotik API class
	private $debug = true;
	
	/**
	* Connect to AP
	*/
	public function __construct($apid) {
		if(!$this->ap = \Doctrine::getTable('AP')->find($apid)) throw new \NotFoundException;
		
		// Connect to API
		$this->api = new \RouterOS($this->ap->IP, $this->ap->username, $this->ap->pass);
	}
	
	/**
	* Get's SSH shell
	*/
	protected function _getShell() {
		if($this->shell) return $this->shell;
		$this->_getMk();
		
		// Try to get Shell
		$this->debug('getting shell');
		$this->shell = $this->mk->ssh->_getShell();
		$this->debug(' got shell');
		
		return $this->shell;
	}
	
	protected function _getMk() {
		if(!isset($this->mk)) {
			// Connect to SSH
			$this->mk = new \mikrotikos($this->ap->IP, 22, $this->ap->username, $this->ap->pass);
			$this->mk->setDebug($this->debug);
		}
		return $this->mk;
	}
	
	function __get($name) {
		switch(strtolower($name)) {
			case 'ap': return $this->ap;
			case 'api': return $this->api;
		}
	}
	
	public function setDebug($mode) {
		$this->debug = $mode;
		if($this->mk) $this->mk->setDebug($mode);
	}
	
	protected function debug($text) {
		if($this->debug) echo date('Y-m-d H:i:s') . "\t$text\n";
	}
	
	/**
	* Test connection to AP
	*/
	public function testConnection() {
		try {
			if($this->api->getVersion()) $connectionOK = true;
			else $connectionOK = false;
		}
		catch(Exception $e) {
			$connectionOK = false;
		}
		
		// Try to reconnect
		if(!$connectionOK) {
			$this->debug("lost connection, disconnecting");
			$this->api->disconnect();
			sleep(2);
			
			$this->debug("connecting again");
			$this->api->connect($this->ap->IP, $this->ap->username, $this->ap->pass);
			$version = $this->api->getVersion();
			$this->debug(" connected, version $version");
		}
		
		return $connectionOK;
	}
	
	/**
	* Get system name
	*/
	public function getSysName() {
		$ret = $this->api->getall('system identity');
		return $ret[0]['name'];
	}
	
	/**
	* Get uptime in seconds
	*/
	public function getUptime() {
		$ret = $this->api->getall('system resource');
		$tim = $ret[0]['uptime'];
		
		return $this->parseUptime($tim);
	}
	
	private function parseUptime($tim) {
		if(preg_match('/^(?:(\d+)w)?(?:(\d+)d)?(\d{2}):(\d{2}):(\d{2})$/', $tim, $match)) {
			list(, $weeks, $days, $h, $m, $s) = $match;
			return ((((($weeks * 7) + $days) * 24) + $h) * 60 + $m) * 60 + $s;
		}
		else return -1;
	}
	
	/**
	* Get basic system info
	*/
	public function getSysInfo() {
		$ret = $this->api->getall('system resource');
		
		// Parse uptime
		$uptime = $this->parseUptime($ret[0]['uptime']);
		
		// Parse version
		if(preg_match('|^(\d+)\\.(\d+)|', $ret[0]['version'], $match)) list(, $vMajor, $vMinor) = $match;
		else {
			$vMajor = (int) $ret[0]['version'];
			$vMinor = null;
		}
		
		return /*new \Thrift\APos\OSInfo*/(array(
			'ap'	=> $this->ap->ID,
			'name'	=> $this->getSysName(),
			'vMajor'=> $vMajor,
			'vMinor'=> $vMinor,
			'version' => $ret[0]['version'],
			'uptime'  => $uptime,
			'ip'	  => $this->ap->IP,
		));
	}
	
	/**
	* Get list of visible MAC addresses
	*/
	public function getMacList($vlan = null, $ifName = null) {
		$ret = $this->api->getall('ip arp');
		$ap = $this->ap;
		return array_map(function($row) use($ap) {
			$static = $row['dynamic'] == 'false' || $row['dynamic'] == 'no';
			$active = $row['disabled'] == 'false' || $row['disabled'] == 'no';
			
			return /*new \Thrift\APos\MacAddressEntry*/(array(
				'ap'	=> $ap->ID,
				'mac'	=> getMac($row['mac-address']),
				'ifName'=> $row['interface'],
				'state'	=> $active ? ($static ? 'configured' : 'learned') : 'unknown',
			));
		}, $ret);
	}
	
	/**
	* Get ARP list
	*/
	public function getArpList($vlan = null, $ifName = null) {
		$ret = $this->api->getall('ip arp');
		$ap = $this->ap;
		return array_map(function($row) use($ap) {
			return /*new \Thrift\APos\ArpEntry*/(array(
				'ap'	=> $ap->ID,
				'ip'	=> $row['address'],
				'mac'	=> getMac($row['mac-address']),
				'ifName'=> $row['interface'],
				'isStatic' => $row['dynamic'] == 'false' || $row['dynamic'] == 'no',
				'isActive' => $row['disabled'] == 'false' || $row['disabled'] == 'no',
			));
		}, $ret);
	}
	
	public function getVlanList() {}
	public function getVlanPortList() {}
	
	/**
	* Get list of routes
	*/
	public function getRouteList($allowDynamic = true) {
		// Find ARP for resolving MAC address or gateway
		$arpList = array();
		foreach($this->api->getall('ip arp') as $row) $arpList[$row['address']] = getMac($row['mac-address']);
		
		$ret = $this->api->getall('ip route');
		$ap = $this->ap;
		return array_map(function($row) use($ap, $arpList) {
			list($dst, $netmask) = explode('/', $row['dst-address']);
			
			return /*new \Thrift\APos\RouteEntry*/(array(
				'ap'		=> $ap->ID,
				'destination'	=> $dst,
				'netmask'	=> $netmask,
				'gateway'	=> (string) @$row['gateway'],
				'cost'		=> @$row['distance'],
				'mac'		=> isset($row['gateway']) ? @$arpList[$row['gateway']] : null,
				'ifName'	=> $row['interface'],
				'isStatic'	=> !isset($row['dynamic']) || isFalse($row['dynamic']),
				'isActive'	=> isFalse($row['disabled']),
			));
		}, $ret);
	}
	
	/**
	* Get list of IP addresses
	*/
	public function getIPList() {
		$ret = $this->api->getall('ip address');
		$ap = $this->ap;
		return array_map(function($row) use($ap) {
			list($ip, $netmask) = explode('/', $row['address']);
			
			return /*new \Thrift\APos\IpAddressEntry*/(array(
				'ap'		=> $ap->ID,
				'ip'		=> $ip,
				'netmask'	=> $netmask,
				'ifName'	=> $row['interface'],
				'isEnabled'	=> isFalse($row['disabled']),
			));
		}, $ret);
	}
	
	/**
	* Get network interfaces
	*/
	public function getInterfaceList() {
		// Clear cached stats
		$this->_clearInterfaceStats();
		
		$ret = $this->api->getall('interface');
		$ap = $this->ap; $oThis = $this;
		$result = array_map(array($this, '_getInterfaceList'), $ret);
		
		unset($this->_wifiInfo);
		return $result;
	}
	
	function _getInterfaceList($row) {
		$if = /*new \Thrift\APos\NetInterfaceEntry*/(array(
			'ap'		=> $this->ap->ID,
			'ifName'	=> $row['name'],
			'type'		=> $row['type'],
			'mtu'		=> $row['mtu'],
			'isEnabled'	=> isFalse($row['disabled']),
			'isActive'	=> isTrue($row['running']),
		));
		
		// Add wireless info
		if($row['type'] == 'wlan') {
			$wifi = $this->_getWirelessIfInfo($row['name']);
			$if['wireless'] = /*new \Thrift\APos\WirelessIfInfo*/(array(
				'bssid'		=> getMac($wifi['mac-address']),
				'essid'		=> $wifi['ssid'],
				'band'		=> $this->getBandId($wifi['band']),
				'frequency'	=> $wifi['frequency'],
			));
			
			// Security profile
			if($sec = $this->_getSecurityProfile($wifi['security-profile'])) {
				switch($sec['mode']) {
					case 'none':
						$enc = array('type' => 'none');
						break;
					
					case 'dynamic-keys':
						$enc = array(
							'type'		=> 'wpa',
							'passphrase'	=> $sec['wpa-pre-shared-key'],
						);
						break;
					
					case 'static-keys-optional':
					case 'static-keys-required':
						$enc = array(
							'type'		=> 'wep',
							'keys'		=> $sec['static-' . $sec['static-transmit-key']],
						);
						break;
				}
				
				if(isset($enc)) $if['wireless']['encryption'] = $enc;
			}
		}
		
		return $if;
	}
	
	/**
	* Clear cached info for getInterfaceList function
	*/
	private function _clearInterfaceStats() {
		$this->_wifiInfo = $this->_securityProfiles = null;
	}
	
	/**
	* Get info about wireless interface
	*/
	private function _getWirelessIfInfo($if) {
		if(is_null($this->_wifiInfo)) $this->_wifiInfo = indexBy($this->api->getall('interface wireless'), 'name');
		return @$this->_wifiInfo[$if];
	}
	
	/**
	* Get security profile of given name
	*/
	private function _getSecurityProfile($name) {
		if(is_null($this->_securityProfiles)) $this->_securityProfiles = indexBy($this->api->getall('interface wireless security-profiles'), 'name');
		return @$this->_securityProfiles[$name];
	}
	
	private function getBandId($name) {
		switch($name) {
			case '2.4ghz': return 'b2';
			case '5ghz': return 'b5';
		}
	}
	
	public function getPortList() {}
	
	
	/**
	* Associated clients (Registration Table)
	*/
	public function getRegistrationTable() {
		$ret = $this->api->getall('interface wireless registration-table');
		$ap = $this->ap;
		return array_map(array($this, '_getRegistrationTable'), $ret);
	}
	
	function _getRegistrationTable($row) {
		return /*new \Thrift\APos\RegistrationTableEntry*/(array(
			'ap'		=> $this->ap->ID,
			'ifName'	=> $row['interface'],
			'mac'		=> getMac($row['mac-address']),
			'radioName'	=> $row['radio-name'],
			'uptime'	=> $this->parseUptime($row['uptime']),
			'snr'		=> $row['signal-to-noise'],
			'lastIP'	=> $row['last-ip'],
		));
	}
	
	/**
	* Execute command on SSH
	*/
	public function execute($command) {
		return (string) $this->mk->cmd($command);
	}
	
	/**
	* Execute more commands at once
	*/
	public function executeList($commandList) {
		$mk = $this->mk;
		return array_map(function($cmd) use($mk) { return (string) $mk->cmd($cmd); }, $commandList);
	}
	
	/**
	* Get service class
	* @param string $serviceName Name of service class
	* @return APService
	*/
	private function _getService($serviceName) {
		$file = __DIR__ . "/services/$serviceName.php";
		if(!file_exists($file)) throw new NotFoundException("APOs service $serviceName");
		
		// Load file
		require_once $file;
		$class = '\APos\Handlers\Mikrotik\Services\\' . $serviceName;
		return new $class($this, $serviceName);
	}
	
	/**
	* Check if service is active
	*/
	public function checkService($serviceName) {
		$x = $this->_getService($serviceName)->check();
		
		if(is_array($x)) @list($isRunning, $text) = $x;
		else { $isRunning = (bool) $x; $text = null; }
		
		return new \Thrift\APos\ServiceState(array(
			'ap'		=> $this->ap->ID,
			'serviceName'	=> $serviceName,
			'isRunning'	=> $isRunning,
			'state'		=> $text,
		));
	}
	
	/**
	* Activate service
	*/
	public function activateService($serviceName) {
		return $this->_getService($serviceName)->activate();
	}
	
	/**
	* Deactivate service
	*/
	public function deactivateService($serviceName) {
		return $this->_getService($serviceName)->deactivate();
	}
	
	public function isSupported($serviceName) {
		try {
			return $this->_getService($serviceName)->isSupported();
		}
		catch(NotFoundException $e) {
			return false;
		}
	}
	
	/**
	* Get list of available services
	*/
	public function getAvailableServices() {
		$ret = array();
		
		// Load all service files
		foreach(glob(__DIR__ . '/services/*.php') as $file) {
			require_once $file;
			
			$serviceName = substr(basename($file), 0, -4);
			$class = '\APos\Handlers\Mikrotik\Services\\' . $serviceName;
			if(!class_exists($class)) continue;
			
			$ret[] = /*new \Thrift\APos\ServiceDescriptor*/(array(
				'ap'		=> $this->ap->ID,
				'serviceName'	=> $serviceName,
				'description'	=> $class::$description,
			));
		}
		
		return $ret;
	}
	
	/**
	* Check all services
	*/
	public function checkAllServices() {
		$ret = array();
		foreach($this->getAvailableServices() as $sDesc) {
			$serviceName = $sDesc['serviceName'];
			$ret[$serviceName] = $this->checkService($serviceName);
		}
		return $ret;
	}
	
	public function export($path) {}
	
	/**
	* Print items from section
	*/
	public function getAll($path) {
		return $this->api->getall($path);
	}
	
	/**
	* Execute commands thru mikrotik API
	*/
	public function executeAPI($path, $cmd, $params) {
		list($retDone, $retData) = $this->api->execute($path, $cmd, $params);
		return array(
			'lst'	=> $retData,
			'ret'	=> $retDone,
		);
	}
	
	/**
	* Execute multiple commands
	*/
	public function executeAPIMulti($cmdList) {
		$ret = array();
		foreach($cmdList as $key => $cmd) {
			$ret[$key] = $this->executeAPI($cmd['path'], $cmd['command'], @$cmd['params']);
		}
		return $ret;
	}
}