<?php
/**
* Connection class for Mikrotik
*/
namespace APos\Handlers;


/**
 * Command handler for Mikrotik OS
 */
class MkHandler implements \Thrift\APos\MkIf {
  /** @var AP Access point */
	private $ap;

  /** @var \Mikrotik\Mikrotik Connection to mikrotik */
  private $mk;

  /** @var bool Show debug information */
	private $debug = true;



	/**
	* Connect to AP
	*/
	public function __construct(\AP $ap) {
    $this->ap = $ap;
    $this->mk = new \Mikrotik\Mikrotik($ap);
	}


  /*******     Internal methods     *********/

  /**
   * Get's AP
   * @return \AP
   */
  function getAP() {
    return $this->ap;
  }

  /**
   * Get class for unified communication with mikrotik
   * @return \Mikrotik\Mikrotik
   */
  public function getMikrotik() {
    return $this->mk;
  }

  /**
   * Gets ssh client
   * @return \Mikrotik\SSHClient
   */
  public function getSSH() {
    return $this->mk->getSSH();
  }

  /**
   * Get RouterOS API client
   * @return \Mikrotik\RouterOS
   */
  public function getROS() {
    return $this->mk->getROS();
  }

  /**
   * Get version of connected mikrotik router
   * @return string
   */
  public function getVersion() {
    return $this->mk->getVersion();
  }

  function getDebug() {
    return $this->debug;
  }

  function setDebug($x) {
    $this->debug = $x;
    $this->mk->setDebug($x);
  }

	protected function debug($text) {
		if($this->debug) echo date('Y-m-d H:i:s') . "\t$text\n";
	}



  /**************  Implemetation of MkIf  *******************/

	/**
	* Test connection to AP
	*/
	public function testConnection() {
    // Try to version from existing connection
    if($this->mk->isROSConnected()) {
      $ret = $this->mk->getROS()->getVersion();
    }
    elseif($this->mk->isSSHConnected()) {
      $ret = $this->mk->getSSH()->getVersion();
    }
    else {
      $ret = $this->mk->getSSH()->getVersion();
    }

    $conencted = !empty($ret);
    if(!$conencted) {
      // TODO: force reconnect
    }

    return $conencted;
	}
	
	/**
	* Get system name
	*/
	public function getSysName() {
		$ret = $this->getROS()->getall('system identity');
		return $ret[0]['name'];
	}
	
	/**
	* Get uptime in seconds
	*/
	public function getUptime() {
		$ret = $this->getROS()->getall('system resource');
		$tim = $ret[0]['uptime'];
		
		return $this->_parseUptime($tim);
	}
	
	private function _parseUptime($tim) {
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
		$ret = $this->getROS->getall('system resource');
		
		// Parse uptime
		$uptime = $this->parseUptime($ret[0]['uptime']);
		
		// Parse version
		if(preg_match('|^(\d+)\\.(\d+)|', $ret[0]['version'], $match)) list(, $vMajor, $vMinor) = $match;
		else {
			$vMajor = (int) $ret[0]['version'];
			$vMinor = null;
		}
		
		return /*new \Thrift\APos\OSInfo*/(array(
			'ap'	  => $this->ap->ID,
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
		$ret = $this->getROS()->getall('ip arp');
		$ap = $this->ap;
		return array_map(function($row) use($ap) {
			$static = $row['dynamic'] == 'false' || $row['dynamic'] == 'no';
			$active = $row['disabled'] == 'false' || $row['disabled'] == 'no';
			
			return /*new \Thrift\APos\MacAddressEntry*/(array(
				'ap'	=> $ap->ID,
				'mac'	=> \Net::getMac($row['mac-address']),
				'ifName'=> $row['interface'],
				'state'	=> $active ? ($static ? 'configured' : 'learned') : 'unknown',
			));
		}, $ret);
	}
	
	/**
	* Get ARP list
	*/
	public function getArpList($vlan = null, $ifName = null) {
		$ret = $this->getROS()->getall('ip arp');
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
		foreach($this->getROS()->getall('ip arp') as $row) $arpList[$row['address']] = \Net::getMac($row['mac-address']);
		
		$ret = $this->getROS()->getall('ip route');
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
				'isActive'	=> \String::isFalse($row['disabled']),
			));
		}, $ret);
	}
	
	/**
	* Get list of IP addresses
	*/
	public function getIPList() {
		$ret = $this->getROS()->getall('ip address');
		$ap = $this->ap;
		return array_map(function($row) use($ap) {
			list($ip, $netmask) = explode('/', $row['address']);
			
			return /*new \Thrift\APos\IpAddressEntry*/(array(
				'ap'		=> $ap->ID,
				'ip'		=> $ip,
				'netmask'	=> $netmask,
				'ifName'	=> $row['interface'],
				'isEnabled'	=> \String::isFalse($row['disabled']),
			));
		}, $ret);
	}
	
	/**
	* Get network interfaces
	*/
	public function getInterfaceList() {
		// Clear cached stats
		$this->_clearInterfaceStats();
		
		$ret = $this->getROS()->getall('interface');
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
			'isEnabled'	=> \String::isFalse($row['disabled']),
			'isActive'	=> isTrue($row['running']),
		));
		
		// Add wireless info
		if($row['type'] == 'wlan') {
			$wifi = $this->_getWirelessIfInfo($row['name']);
			$if['wireless'] = /*new \Thrift\APos\WirelessIfInfo*/(array(
				'bssid'		=> \Net::getMac($wifi['mac-address']),
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
		if(is_null($this->_wifiInfo)) $this->_wifiInfo = indexBy($this->getROS()->getall('interface wireless'), 'name');
		return @$this->_wifiInfo[$if];
	}
	
	/**
	* Get security profile of given name
	*/
	private function _getSecurityProfile($name) {
		if(is_null($this->_securityProfiles)) $this->_securityProfiles = indexBy($this->getROS()->getall('interface wireless security-profiles'), 'name');
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
		$ret = $this->getROS()->getall('interface wireless registration-table');
		$ap = $this->ap;
		return array_map(array($this, '_getRegistrationTable'), $ret);
	}
	
	function _getRegistrationTable($row) {
		return /*new \Thrift\APos\RegistrationTableEntry*/(array(
			'ap'		=> $this->ap->ID,
			'ifName'	=> $row['interface'],
			'mac'		=> \Net::getMac($row['mac-address']),
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
    return (string) $this->mk->getSSH()->execShellWait($command);
	}
	
	/**
	* Execute more commands at once
	*/
	public function executeList($commandList) {
    return array_map(array($this, 'execute'), $commandList);
	}
	
	/**
	* Get service class
	* @param string $serviceName Name of service class
	* @return APService
	*/
	private function _getService($serviceName) {
		$class = '\APos\Handlers\Mikrotik\Services\\' . $serviceName;
    if(!class_exists($class)) throw new \NotFoundException("APOs service $serviceName");
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
		catch(\NotFoundException $e) {
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



  /********    MkIf interface implementation    ********/

	public function export($path) {
    return $this->mk->export($path);
  }
	
	/**
	* Print items from section
	*/
	public function getAll($path) {
		return $this->getROS()->getall($path);
	}
	
	/**
	* Execute commands thru mikrotik API
	*/
	public function executeAPI($path, $cmd, $params) {
		list($retDone, $retData) = $this->getROS()->execute($path, $cmd, $params);
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

  /**
   * Add new item to MK
   * @param string $path
   * @param array $args
   * @return string ID of new itme
   */
  public function add($path, $args) {
    return $this->getROS()->add($path, $args);
  }

  /**
   * Add multiple items at once
   * @param string $path
   * @param string $list Array of items
   * @return array List of IDs of newly added items
   */
  public function addMulti($path, $list) {
    $ret = array();
    foreach($list as $k => $v) $ret[$k] = $this->add($path, $list);

    return $ret;
  }
}
