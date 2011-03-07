<?php

/**
 * Test initialization and helpers.
 *
 * @author     David Grudl
 * @package    Nette\Test
 */

require __DIR__ . '/NetteTest/TestHelpers.php';
require __DIR__ . '/NetteTest/Assert.php';
require __DIR__ . '/../libs/Nette/loader.php';

// configure environment
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', TRUE);
ini_set('html_errors', FALSE);
ini_set('log_errors', FALSE);

$_SERVER = array_intersect_key($_SERVER, array_flip(array('argc', 'argv', 'PHP_SELF', 'SCRIPT_NAME', 'SERVER_ADDR', 'SERVER_SOFTWARE', 'HTTP_HOST', 'DOCUMENT_ROOT', 'OS')));
$_SERVER['REQUEST_TIME'] = 1234567890;
$_ENV = array();

if (PHP_SAPI !== 'cli') {
	header('Content-Type: text/plain; charset=utf-8');
}

if (extension_loaded('xdebug')) {
	xdebug_disable();
	TestHelpers::startCodeCoverage(__DIR__ . '/coverage.dat');
}

// absolute filesystem path to the application root
define('APP_DIR', realpath(__DIR__ . '/../app'));
define('LIBS_DIR', realpath(APP_DIR . '/../libs'));
define('TMP_DIR', realpath(APP_DIR . '/../temp/'));
define('LOG_DIR', realpath(APP_DIR . '/../log/'));

// Load config
Nette\Environment::loadConfig(APP_DIR . '/config.ini');
