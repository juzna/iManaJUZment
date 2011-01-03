<?php
/**
* Loads Thrift and all it's packages
*/

require_once __DIR__ . '/Thrift.php';
require_once __DIR__ . '/protocol/TBinaryProtocol.php';
require_once __DIR__ . '/transport/TSocket.php';
require_once __DIR__ . '/transport/TBufferedTransport.php';

foreach(glob(__DIR__ . '/packages/*/*.php') as $file) require_once $file;
