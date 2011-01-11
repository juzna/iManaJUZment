<?php
/**
* Sample APos connection class
*/
namespace APos\Handlers;


/**
 * Dummy APos handler
 */
class NullHandler implements \Thrift\APos\APosIf {
	/**
	* Constructor for this AP
	*/
	public function __construct() {
		// placeholder
	}

	public function getSysName() {
		return 'No name';
	}
	
	public function getUptime() {
		return 123;
	}
	
	public function getSysInfo() {
		return new \Thrift\APos\OSInfo(array(
			'ap'	=> 1,
			'name'	=> 'No name',
			'vMajor'=> 1,
			'vMinor'=> 99,
			'version' => '1.99.13',
			'uptime'  => $this->getUptime(),
			'ip'	  => '127.0.0.1',
		));
	}
	
	public function getMacList($vlan, $ifName) {}
	public function getArpList($vlan, $ifName) {}
	public function getVlanList() {}
	public function getVlanPortList() {}
	public function getRouteList($allowDynamic) {}
	public function getIPList() {}
	public function getInterfaceList() {}
	public function getPortList() {}
	public function getRegistrationTable() {}
	public function execute($command) {}
	public function executeList($commandList) {}
	public function testConnection() {}
	public function checkService($serviceName) {}
	public function activateService($serviceName) {}
	public function deactivateService($serviceName) {}
	public function getAvailableServices() {}
	public function isSupported($serviceName) {}
  public function checkAllServices() {}
}