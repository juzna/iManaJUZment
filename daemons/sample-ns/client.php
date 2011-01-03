<?php
$GLOBALS['THRIFT_ROOT'] = realpath(__DIR__ . '/../../libs/thrift/');

require_once $GLOBALS['THRIFT_ROOT'].'/Thrift.php';
require_once $GLOBALS['THRIFT_ROOT'].'/protocol/TBinaryProtocol.php';
require_once $GLOBALS['THRIFT_ROOT'].'/transport/TSocket.php';
require_once $GLOBALS['THRIFT_ROOT'].'/transport/THttpClient.php';
require_once $GLOBALS['THRIFT_ROOT'].'/transport/TBufferedTransport.php';

// Load service
require_once $GLOBALS['THRIFT_ROOT'].'/packages/tutorial/Calculator.php';


try {
  $unixPath = realpath(__DIR__ . '/../../') . '/tmp/sock/sample';

  $socket = new TSocket("unix://$unixPath", -1);
  $transport = new TBufferedTransport($socket, 1024, 1024);
  $protocol = new TBinaryProtocol($transport);
  $client = new \Thrift\SharedServiceClient($protocol);

  $transport->open();
  
  var_dump($client->getStruct(1));
  var_dump($client->getStruct(2));
  var_dump($client->getStruct(3));
 
  $transport->close();
 
} catch (TException $tx) {
  print 'TException: '.$tx->getMessage()."\n";
}
