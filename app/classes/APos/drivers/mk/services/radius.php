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

namespace APos\Handlers\Mikrotik\Services;

/**
Example of communication

/radius/print
Array
(
    [.id] => *1
    [service] => dhcp
    [called-id] =>
    [domain] =>
    [address] => 85.132.153.5
    [secret] => hujhujclovece
    [authentication-port] => 1812
    [accounting-port] => 1813
    [timeout] => 00:00:00.300
    [accounting-backup] => false
    [realm] => Vrobelova-doma
    [disabled] => false
    [comment] =>
)

*/

class Radius extends APService {
	static $description = 'Radius';
	
	/**
	* Check if service is running
	* @return array(bool, string)
	*/
	public function check() {
		$sett = $this->getROS()->getall('radius');
		return count($sett) > 0;
	}
	
	/**
	* Activate service
	*/
	public function activate() {
		$api = $this->getROS();
		
		// Generate realm
		if(empty($this->ap->realm)) {
			$this->ap->realm = $this->ap->nazev;
			$this->ap->save();
		}
		$realm = $this->ap->realm;
		
		// Default config
		$config = array(
			'service'		=> 'ppp,hotspot,wireless,dhcp',
			'authentication-port'	=> 1812,
			'accounting-port'	=> 1813,
			'realm'			=> $realm,
			'disabled'		=> 'false',
		);
		
		// Per server config
		$servers = array(
			array('address' => '85.153.153.5', 'secret' => 'hujhujclovece'),
			array('address' => '85.153.153.1', 'secret' => 'bujakacha'),
		);
		
		// Find existing servers
		$existing = indexBy($api->getall('radius'), 'address');
		
		// Save servers to MK
		foreach($servers as $server) {
			$cfg = array_merge($config, $server);
			
			// Existing?
			if(isset($existing[$cfg['address']])) {
				$cfg['.id'] = $existing[$cfg['address']]['.id'];
				$api->request('/radius/set', $cfg);
			}
			// Adding new
			else {
				$api->request('/radius/add', $cfg);
			}
		}
	}
	
	/**
	* Deactivate
	*/
	public function deactivate() {
		$this->getROS()->request('/system/ntp/client/set', array(
			'enabled'	=> 'false',
		));
	}
	
	/**
	* Check if this AP supports it
	* @return bool
	*/
	public function isSupported() {
		return true; // Always supported
	}
}