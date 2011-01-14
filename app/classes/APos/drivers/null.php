<?php
/**
 * This file is part of the "iManaJUZment" - complex system for internet service providers
 *
 * Copyright (c) 2005 - 2011 Jan Dolecek (http://juzna.cz)
 *
 * iManaJUZment is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with iManaJUZment.  If not, see <http://www.gnu.org/licenses/gpl.txt>.
 *
 * @license http://www.gnu.org/licenses/gpl.txt
 */

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