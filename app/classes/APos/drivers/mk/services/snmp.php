<?php
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
		$settings = $this->getApi()->getall('snmp');
		$communities = $this->getApi()->getall('snmp community');
		return array($settings[0], $communities);
	}

	/**
	* Check if service is running
	* @return array(bool, string)
	*/
	public function check() {
		list($sett, $coms) = $this->getConfig();
		
		if(isFalse($sett['enabled'])) return false; // Disabled
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
		$api = $this->getApi();
		list($sett, $coms) = $this->getConfig();
		
		// Prepare community name
		$comName = $this->ap->snmpCommunity ?: (sizeof($coms) ? $coms[0]['name'] : "servisweb-" . randchar(8));
		
		// Enable it
		$api->request('/snmp/set', array('enabled' => 'true'));
		
		// Check if community exists
		$communityExists = false;
		foreach($coms as $com) if($com['name'] == $comName) $communityExists = true;
		
		// Add community
		if(!$communityExists) $api->add('snmp community', array('name' => $comName));
		
		// Save changes to DB
		$this->ap->snmpAllowed = true;
		$this->ap->snmpCommunity = $comName;
		$this->ap->save();
	}
	
	/**
	* Deactivate
	*/
	public function deactivate() {
		$this->getApi()->request('/snmp/set', array('enabled' => 'false'));

		// Save changes to DB
		$this->ap->snmpAllowed = false;
		$this->ap->save();
	}
	
	/**
	* Check if this AP supports it
	* @return bool
	*/
	public function isSupported() {
		return true; // Always supported
	}
}