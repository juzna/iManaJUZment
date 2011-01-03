<?php
/**
* Dwarf for an particular AP, which connects to it and executes commands
*/
$wantDoctrine = true; // Tell bootstrap what we want
require_once(__DIR__ . '/../bootstrap.php');

if(!is_numeric($apid = @$_SERVER['argv'][1])) die("Missing AP's ID\n");

// Find OS
$os = mr("select `os` from AP where ID='$apid'", 'data');
if(!$os) throw new NotFoundException("AP or it's operating system not found");

// Find driver
$driver = APos::getDriverName($os);

// Direct connector
$direct = APos::getConnector('direct');
$handler = $direct->create($driver, $apid);

// Connector
$connector = APos::getConnector('thrift');
$connector->onServerReady = 'APos_server_onReady';
$connector->createServer($driver, $apid, $handler);

echo "Finished\n";


function APos_server_onReady($conn) {
	global $readyPath;
	echo "Server is ready\n";
	
	// Confirmation that we're ready
	$fp = dio_get(3);
	dio_write($fp, "OK\n");
	dio_close($fp);
}
