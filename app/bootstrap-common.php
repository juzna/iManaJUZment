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
$conn->setCharset('utf8');
$conn->setCollate('ut­f8_general_ci');


// Initialize extensions
$manager = Doctrine_Manager::getInstance();
$doctrineExtensionDir = LIBS_DIR . "/doctrine-ext/";
// Doctrine::setExtensionsPath(realpath($doctrineExtensionDir));
// $manager->registerExtension('Sortable');
// $manager->registerExtension('Taggable');



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


// Run Doctrine profiler
if ($dbConfig->profiler) {
  $profiler = new Doctrine_Connection_Profiler();
  $conn->setListener($profiler);
//  Nette\Debug::enableProfiler();
//  Nette\Debug::addColophon('fetchDoctrineEvents');
}


// Profiler callback
function fetchDoctrineEvents()
{
    $profiler = Doctrine_Manager::getInstance()->getCurrentConnection()->getListener();

    $queries = 0;
    $out = '<br />';
    foreach ($profiler as $event) {
        $evName = $event->getName();

        if ($evName == 'execute') {
            $queries++;
            $out .= '[' . number_format($event->getElapsedSecs() * 1000, 3) . 'ms]<br />'. $event->getQuery() . '<br />';
        }

        $params = $event->getParams();
        if(!empty($params)) {
            $out .= print_r($params, true) . '<br /><br />';
        }
    }

    return array(
        $profiler->count() . ' Doctrine events',
        $queries . ' sql queries',
        $out
    );
}
