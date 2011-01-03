<?php
/**
* Master daemon for reading data from SNMP
* Manages child processes for actual reading snmp data from devices
*/

error_reporting(E_ALL);
ob_implicit_flush();

define('RUNPAGE', 'MAIN'); // What kind of processing we do
define('APP_DIR', realpath(__DIR__ . "/../../"));
define('INTERVAL', 300);


require_once __DIR__ . "/../../bootstrap.php";
require_once __DIR__ . "/../../3rdParty/thrift/bootstrap.php";

while(true) {
	$childPath = __DIR__ . "/child.php";
	
	// Fork child processes
	$aps = Doctrine::getTable('AP')->findBySnmpAllowed(true);
	foreach($aps as $ap) {
		$cmd = "php -f '$childPath' '$ap->ID' '$ap->IP' '$ap->snmpCommunity'";
		echo "$cmd\n";
		exec("$cmd &");
	}
	
	// Sleep
	$t = (time() + INTERVAL) % INTERVAL;
	$d = time() - $t;
	if($d < INTERVAL / 2) $d += INTERVAL;
	echo "sleeping for $d seconds\n"; flush();
	sleep($d);
}