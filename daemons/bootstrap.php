<?php
/**
* Basic initializer for all daemons
*/

// absolute filesystem path to the application root
define('APP_DIR', realpath(__DIR__ . '/../app/'));
define('LIBS_DIR', realpath(APP_DIR . '/../libs/'));
define('TMP_DIR', realpath(APP_DIR . '/../temp/'));
define('LOG_DIR', realpath(APP_DIR . '/../log/'));

require_once __DIR__ . '/common.php';

// Flush automatically
ob_implicit_flush(true);

// Load Nette framweork
require_once(LIBS_DIR . '/Nette/loader.php');

use Nette\Environment;
Nette\Debug::enable();
Environment::loadConfig();

// Load Doctrine as well
if(isset($wantDoctrine) && $wantDoctrine) require_once APP_DIR . '/bootstrap-doctrine.php';

