<?php
/**
* @package ServisWeb
* @subpackage Network
*/

/**
* Prace s IP adresami a subnety
*/
class IPAddress {
	/**
	* Zjisti zda IP adresa partri do podsite
	* @param ip $ip Testovana IP adresa
	* @param ip $subnet IP adresa podsite
	* @param int $mask Maska podsite v kratkem tvaru
	* @return bool Zda tam patri nebo ne
	*/
	public static function IPinSubnet($ip, $subnet, $mask) {
		// Prevedeme na long
		$mask = netmask($mask);

		$ip = is_numeric($ip) ? $ip : ip2long($ip);
		$subnet = is_numeric($subnet) ? $subnet : ip2long($subnet);

		return ($ip & $mask) == ($subnet & $mask);
	}
	
	/**
	* Whether is subnet A included in subnet B
	*/
	public static function subnetInSubnet($netA, $maskA, $netB, $maskB) {
		// Prevedeme na long
		$maskA = netmask($maskA);
		$maskB = netmask($maskB);
		if(!is_numeric($netA)) $netA = ip2long($netA);
		if(!is_numeric($netB)) $netB = ip2long($netB);
		
		// Equal mask
		if($maskA == $maskB) return ($netA & $maskA) == ($netB & $maskB);
		
		// Subnet A is greater (in IP address count)
		elseif($maskA < $maskB) return false;
		
		// Subnet A is smaller
		else return ($netA & $maskB) == ($netB & $maskB);
	}
}

