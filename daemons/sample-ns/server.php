<?php
error_reporting(E_ALL);
$GLOBALS['THRIFT_ROOT'] = realpath(__DIR__ . '/../../libs/thrift/');

require_once $GLOBALS['THRIFT_ROOT'].'/Thrift.php';
require_once $GLOBALS['THRIFT_ROOT'].'/protocol/TBinaryProtocol.php';
require_once $GLOBALS['THRIFT_ROOT'].'/transport/TSocket.php';
require_once $GLOBALS['THRIFT_ROOT'].'/transport/THttpClient.php';
require_once $GLOBALS['THRIFT_ROOT'].'/transport/TBufferedTransport.php';

// Load service
require_once $GLOBALS['THRIFT_ROOT'].'/packages/tutorial/Calculator.php';


class SharedServiceHandler implements \Thrift\SharedServiceIf {
	public function getStruct($key) {
		return new \Thrift\SharedStruct(array('key' => $key, 'value' => "Ahoj $key"));
	}
}

@ob_end_flush();

$handler = new SharedServiceHandler();
$processor = new \Thrift\SharedServiceProcessor($handler);

$unixPath = realpath(__DIR__ . '/../../') . '/tmp/sock/sample';
@unlink($unixPath);
$socket = new TServerSocket("unix://$unixPath", -1);
$socket->listen();
while(true) {
	$socket->select($processor);
	echo "."; flush();
}
