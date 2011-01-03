<?php
/**
* Loads Thrift and all it's packages
*/

// absolute filesystem path to the application root
define('APP_DIR', realpath(__DIR__ . '/../app/'));
define('LIBS_DIR', realpath(APP_DIR . '/../libs/'));
define('TMP_DIR', realpath(APP_DIR . '/../temp/'));
define('LOG_DIR', realpath(APP_DIR . '/../log/'));


define('THRIFT_DIR', __DIR__ . '/../libs/thrift/');
define('THRIFT_PACKAGES', __DIR__ . '/interface/gen-php/');

require_once __DIR__ . '/common.php';
require_once THRIFT_DIR . '/Thrift.php';
require_once THRIFT_DIR . '/protocol/TBinaryProtocol.php';
require_once THRIFT_DIR . '/transport/TSocket.php';
require_once THRIFT_DIR . '/transport/TBufferedTransport.php';

foreach(glob(THRIFT_PACKAGES . '/packages/*/*.php') as $file) require_once $file;

