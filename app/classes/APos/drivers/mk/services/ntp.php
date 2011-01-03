<?php
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

class NTP extends APService {
	static $description = 'Network Time Protocol';
	
	/**
	* Get's current configuration
	*/
	private function getConfig() {
		$clock = $this->getApi()->getall('system clock');
		$ntp = $this->getApi()->getall('system ntp client');
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
		$this->getApi()->request('/system/clock/set', array('time-zone-name' => 'Europe/Prague'));
		$this->getApi()->request('/system/ntp/client/set', array(
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
		$this->getApi()->request('/system/ntp/client/set', array(
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