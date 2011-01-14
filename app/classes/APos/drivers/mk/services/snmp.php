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
/snmp/print
(
    [enabled] => false
    [contact] => 
    [location] => 
    [engine-id] => 
    [engine-boots] => 0
    [time-window] => 15
    [trap-sink] => 0.0.0.0
    [trap-community] => (unknown)
    [trap-version] => 1
)

/snmp/community/print
(
    [.id] => *0
    [name] => public
    [address] => 0.0.0.0/0
    [security] => none
    [read-access] => true
    [write-access] => false
    [authentication-password] => 
    [encryption-password] => 
    [authentication-protocol] => MD5
    [encryption-protocol] => DES
)
*/


class SNMP extends APService {
	static $description = 'Public SNMP';
	
	/**
	* Get's current configuration
	*/
	private function getConfig() {
		$settings = $this->getROS()->getall('snmp');
		$communities = $this->getROS()->getall('snmp community');
		return array($settings[0], $communities);
	}

	/**
	* Check if service is running
	* @return array(bool, string)
	*/
	public function check() {
		list($sett, $coms) = $this->getConfig();
		
		if(\String::isFalse($sett['enabled'])) return false; // Disabled
		if(!$this->ap->snmpAllowed) return false;
		if(!is_array($coms) || !count($coms)) return false; // No communities defined
		
		// Find given community
		foreach($coms as $com) {
			if($com['name'] == $this->ap->snmpCommunity) {
				$this->ap->snmpAllowed = true;
				$this->ap->snmpCommunity = $com['name'];
				$this->ap->save();
				return true;
			}
		}
		
		return false;
	}
	
	/**
	* Activate service
	*/
	public function activate() {
		$api = $this->getROS();
		list($sett, $communities) = $this->getConfig();
		
		// Prepare community name
		$comName = $this->ap->snmpCommunity ?: (sizeof($communities) ? $communities[0]['name'] : "servisweb-" . \String::randchar(8));
		
		// Enable it
		$api->request('/snmp/set', array('enabled' => 'true'));
		
		// Check if community exists
		$communityExists = false;
		foreach($communities as $com) if($com['name'] == $comName) $communityExists = true;
		
		// Add community
		if(!$communityExists) $api->add('snmp community', array('name' => $comName));
		
		// Save changes to DB
		$this->ap->snmpAllowed = true;
		$this->ap->snmpCommunity = $comName;
		$this->ap->flush();
	}
	
	/**
	* Deactivate
	*/
	public function deactivate() {
		$this->getROS()->request('/snmp/set', array('enabled' => 'false'));

		// Save changes to DB
		$this->ap->snmpAllowed = false;
		$this->ap->flush();
	}
	
	/**
	* Check if this AP supports it
	* @return bool
	*/
	public function isSupported() {
		return true; // Always supported
	}
}
