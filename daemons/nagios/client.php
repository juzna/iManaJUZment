<?php
/**
* Tells Nagios server to update it's configuration
*/

$wantDoctrine = true;
require_once(__DIR__ . '/../bootstrap.php');
$unixPath = TMP_DIR . '/sock/nagios';

TBase::$allowEnumConversion = true;
$socket = new TSocket("unix://$unixPath", -1);
$transport = new TBufferedTransport($socket, 1024, 1024);
$protocol = new TBinaryProtocol($transport);
$client = new \Thrift\Nagios\NagiosClient($protocol);

$transport->open();


// Prepare config
$conf = new \Thrift\Nagios\Configuration;

foreach(AP::getRepository()->findAll() as $ap) {
	$conf->hosts[] = new \Thrift\Nagios\HostEntry(array(
		'hostName'	=> str_replace(',', '_', $ap->name),
		'ip'		    => $ap->IP,
		'parents'	  => array(($p = $ap->getL3Parent()) ? str_replace(',', '_', $p->name) : null),
		'services'	=> array('ping','http'),
	));
}

/*	
  1: required string hostName,
  2: optional string hostAlias,
  3: required common.ipAddress ip,
  4: required string contactGroup,
  5: string template = "generic-host",
  6: optional string image,		// Path to image
  7: common.coordinates coords,	// Coordinates for map
  8: string url,		// Action URL
  9: list<string> groups = [ 'default' ],	// List of groups
  10: list<CheckService> services = [ ping ],	// List of services to be checked
  11: required list<string> parents,		// Lis	
*/


// Send config
$client->updateConfiguration($conf);

$transport->close();

