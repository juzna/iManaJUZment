<?php
require_once(__DIR__ . '/../bootstrap.php');

if(!is_numeric($apid = @$_SERVER['argv'][1])) die("Missing AP's ID\n");

// Create client
$client = APos::get($apid, 'thrift');

var_dump($client->testConnection());
var_dump($client->getSysName());
var_dump($client->getUptime());
print_r($client->getSysInfo());
print_r($client->getMacList(null, null));
print_r($client->getArpList(null, null));
print_r($client->getRouteList(true));
print_r($client->getIPList());
print_r($client->getInterfaceList());
print_r($client->getRegistrationTable());

var_dump($snmp = $client->checkService('snmp'));
var_dump($client->isSupported('snmp'));

var_dump($client->activateService('snmp'));
var_dump($client->deactivateService('snmp'));
print_r($client->getAvailableServices());
