<?php
/**
* Master daemon for reading data from SNMP
* Manages child processes for actual reading snmp data from devices
*/

require_once(__DIR__ . '/../bootstrap.php');
define('INTERVAL', 300);


while(true) {
	$childPath = __DIR__ . "/child.php";
	
	// Fork child processes
	$aps = \AP::getRepository()->findBySnmpAllowed(true);
	foreach($aps as $ap) {
		$cmd = "php -f '$childPath' '$ap->ID' '$ap->IP' '$ap->snmpCommunity'";
		echo "$cmd\n";
		exec("$cmd &");
	}
	
	// Sleep until next tick
	$t = (time() + INTERVAL) % INTERVAL;
	$d = time() - $t;
	if($d < INTERVAL / 2) $d += INTERVAL;
	echo "sleeping for $d seconds\n"; flush();
	sleep($d);
}
