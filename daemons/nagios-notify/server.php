<?php
require_once(__DIR__ . '/../bootstrap.php');


/**
* Nagios event listener hadnler
*/
class ListenerHandler implements \Thrift\Nagios\ListenerIf {
	public function processEvent($ev) {
		// TODO: process event
		echo "Got event $ev->hostName is $ev->newState\n";
	}
/*
  1: string hostName,
  2: common.ipAddress hostIp,
  3: string newState	
*/
}



// Create handler
$handler = new ListenerHandler();

// Processor
$processor = new \Thrift\Nagios\ListenerProcessor($handler);

// Run server loop
runUnixSocketServer('nagios-notify', $processor);

