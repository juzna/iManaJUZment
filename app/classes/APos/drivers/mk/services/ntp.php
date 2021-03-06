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

/system/clock/print
Array
(
    [time] => 09:12:22
    [date] => may/13/2010
    [time-zone-name] => Europe/Prague
    [gmt-offset] => 7200
    [dst-active] => true
)

/system/ntp/client/print
Array
(
    [enabled] => true
    [mode] => unicast
    [primary-ntp] => 85.132.153.5
    [secondary-ntp] => 0.0.0.0
    [poll-interval] => 00:15:00
    [active-server] => 85.132.153.5
    [last-update-from] => 85.132.153.5
    [last-update-before] => 00:13:57.510
    [last-adjustment] => 00:00:00.004247
)
*/

/**
 * NTP service
 * Activates automatic time configuration on Mikrotik
 */
class NTP extends APService {
	static $description = 'Network Time Protocol';
	
	/**
	* Get's current configuration
	*/
	private function getConfig() {
		$clock = $this->getROS()->getall('system clock');
		$ntp = $this->getROS()->getall('system ntp client');
		return array($clock[0], $ntp[0]);
	}

	/**
	* Check if service is running
	* @return array(bool, string)
	*/
	public function check() {
		list($clock, $ntp) = $this->getConfig();
		
		if($clock['time-zone-name'] != 'Europe/Prague') return array(false, "Time zone neni nastaveno");
		if(isFalse($ntp['enabled'])) return array(false, "NTP client neni aktivovat");
		
		return false;
	}
	
	/**
	* Activate service
	*/
	public function activate() {
		$this->getROS()->request('/system/clock/set', array('time-zone-name' => 'Europe/Prague'));
		$this->getROS()->request('/system/ntp/client/set', array(
			'enabled'	=> 'true',
			'mode'		=> 'unicast',
			'primary-ntp'	=> '85.132.153.5',
			'secondary-ntp'	=> '195.113.144.238', // tak.cesnet.cz
		));
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
