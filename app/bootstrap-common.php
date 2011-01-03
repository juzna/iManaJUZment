<?php
/**
* Common bootstrap, which is used either for web or console application
*/
require_once(LIBS_DIR . '/Nette/loader.php');

// Step 2: Configure environment
// 2a) enable Nette\Debug for better exception and error visualisation
Nette\Debug::enable();

// 2b) load configuration from config.ini file
Nette\Environment::loadConfig();


// Load Doctrine for DB connections
require_once __DIR__ . '/bootstrap-doctrine.php';

