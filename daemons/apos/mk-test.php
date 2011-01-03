<?php
require_once(__DIR__ . '/../bootstrap.php');
define('HTML_OUT', @$_ENV['HTML'] ? 1 : 0);

if(!is_numeric($apid = @$_SERVER['argv'][1])) die("neni zadano ID APcka\n");

// Create communication socket
$sockets = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP) or die("socket_create_pair() failed.");

$pid = pcntl_fork();
if($pid == -1) die('Could not fork Process.');
elseif($pid) {
    // parent -> server
    fclose($sockets[0]);
    
    // Create transport
    $socket = new TClientSocket($sockets[1]);
    $transport = new TBufferedTransport($socket, 1024, 1024);
    
    // Run server
    processServer($transport);
    
    echo "server end\n";
}
else {
    // child -> client
    fclose($sockets[1]);
    
    // Create transport
    $socket = new TClientSocket($sockets[0]);
    $transport = new TBufferedTransport($socket, 1024, 1024);
    
    // Run client processing
    processClient($transport);
    
    echo "client end\n";
}


// Act as server
function processServer($transport) {
	global $apid;
	
	// Direct connector
	$direct = APos::getConnector('direct');
	$handler = $direct->create('mk', $apid);
	
	// Thrift server
	$server = APos::getConnector('thrift', array('transport' => $transport));
	$server->createServerFromTransport('mk', $transport, $handler);
}

// Act as client
function processClient($transport) {
	global $apid;
	
	// Connector
	$conn = APos::getConnector('thrift', array('transport' => $transport));
	$client = $conn->create('mk', $apid);

	if(HTML_OUT) echo '<pre>';
	
	testClient($client, 'testConnection');
	testClient($client, 'getSysName');
	testClient($client, 'getUptime');
	testClient($client, 'getSysInfo');
	testClient($client, 'getMacList', null, null);
	testClient($client, 'getArpList', null, null);
	testClient($client, 'getRouteList', true);
	testClient($client, 'getIPList');
	testClient($client, 'getInterfaceList');
	testClient($client, 'getRegistrationTable');
	testClient($client, 'checkService', 'snmp');
	testClient($client, 'isSupported', 'snmp');
	testClient($client, 'activateService', 'snmp');
	testClient($client, 'deactivateService', 'snmp');
	testClient($client, 'getAvailableServices');
	testClient($client, 'getall', 'ip address');
	
	if(HTML_OUT) echo '<hr />';
}

function testClient($client, $methodName) {
	$args = func_get_args();
	$args = array_slice($args, 2);
	
	echo HTML_OUT ? "<h1>Testing $methodName</h1>" : "Testing $methodName\n";
	try {
		$ret = call_user_func_array(array($client, $methodName), $args);
		
		if(is_array($ret) && count($ret)) print_r($ret);
		else var_dump($ret);
	}
	catch(Exception $e) {
		echo "Exception: " . $e->getMessage() . "\n";
	}
	
	return $ret;
}

