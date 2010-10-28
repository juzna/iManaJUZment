<?php
/**
* Common bootstrap, which is used either for web or console application
*/
require_once(LIBS_DIR . '/Nette/loader.php');

use Nette\Debug;
use Nette\Environment;



// Step 2: Configure environment
// 2a) enable Nette\Debug for better exception and error visualisation
Debug::enable();

// 2b) load configuration from config.ini file
Environment::loadConfig();


// Connect to database using Doctrine
$dbConfig = Environment::getConfig('database');
$conn = Doctrine_Manager::connection($dbConfig->driver . '://' . $dbConfig->username . ':' . $dbConfig->password . '@' . $dbConfig->host . '/' . $dbConfig->database);


// Set up config variables for Doctrine
Environment::setVariable('doctrine_config',
    array(
        'data_fixtures_path' => __DIR__ . '/doctrine/data/fixtures',
        'models_path'        => __DIR__ . '/models',
        'migrations_path'    => __DIR__ . '/doctrine/migrations',
        'sql_path'           => __DIR__ . '/doctrine/data/sql',
        'yaml_schema_path'   => __DIR__ . '/doctrine/schema'
    )
);
