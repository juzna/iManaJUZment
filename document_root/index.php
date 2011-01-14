<?php

// absolute filesystem path to the web root
define('WWW_DIR', dirname(__FILE__));

// absolute filesystem path to the application root
define('APP_DIR', WWW_DIR . '/../app');

// absolute filesystem path to the libraries
define('LIBS_DIR', WWW_DIR . '/../libs');
define('TMP_DIR', realpath(APP_DIR . '/../temp/'));
define('LOG_DIR', realpath(APP_DIR . '/../log/'));

// load bootstrap file
require APP_DIR . '/bootstrap-web.php';
