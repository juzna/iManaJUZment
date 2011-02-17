#!/usr/bin/php
<?php
/**
* Send an event to LiveConnect server
*/

@list(, $userId, $table, $op) = $_SERVER['argv'];
if(!isset($table)) die("Please pass arguments: user, table, operation!\n");

require_once(__DIR__ . '/../bootstrap.php');
$unixPath = TMP_DIR . '/sock/nagios-notify';

$socket = new TSocket('localhost', 9090);
//$transport = new TBufferedTransport($socket, 1024, 1024);
$transport = new TFramedTransport($socket);
$protocol = new TBinaryProtocol($transport, false, false);
$client = new /*\Thrift\LiveConnect\*/LiveConnectClient($protocol);

$oldData = array('name' => 'John');
$newData = array('name' => 'Simon');

$transport->open();
$client->notify($userId, $table, $op, $oldData, $newData);
$transport->close();

echo "Message sent\n";
