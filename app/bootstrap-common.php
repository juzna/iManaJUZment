<?php
/**
* Common bootstrap, which is used either for web or console application
*/
require_once(LIBS_DIR . '/Nette/loader.php');

use Nette\Environment;


// Step 2: Configure environment
// 2a) enable Nette\Debug for better exception and error visualisation
Nette\Debug::enable();

// 2b) load configuration from config.ini file
Environment::loadConfig();


// Connect to database using Doctrine
$dbConfig = Environment::getConfig('database');
$conn = Doctrine_Manager::connection($dbConfig->driver . '://' . $dbConfig->username . ':' . $dbConfig->password . '@' . $dbConfig->host . '/' . $dbConfig->database);

// Initialize extensions
{
  $manager = Doctrine_Manager::getInstance();
//  spl_autoload_register(array('Doctrine', 'extensionsAutoload'));
  
  $doctrineExtensionDir = LIBS_DIR . "/doctrine-ext/";
  // Doctrine::setExtensionsPath(realpath($doctrineExtensionDir));
  // $manager->registerExtension('Sortable');
  //$manager->registerExtension('Taggable');
}



// Set up config variables for Doctrine
Environment::setVariable('doctrine_config',
    array(
        'data_fixtures_path' => __DIR__ . '/doctrine/data/fixtures',
        'models_path'        => __DIR__ . '/models',
        'migrations_path'    => __DIR__ . '/doctrine/migrations',
        'sql_path'           => __DIR__ . '/doctrine/data/sql',
        'yaml_schema_path'   => __DIR__ . '/doctrine/schema',
        'generate_models_options' => array(
          'tableClassesDirectory' => 'tables',
          'pearStyle'             => true,
          'generateTableClasses'	=> true,
        ),
    )
);
