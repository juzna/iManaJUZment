<?php
/**
 * My Application bootstrap file.
 *
 * @copyright  Copyright (c) 2009 John Doe
 * @package    MyApplication
 */

use Nette\Environment,
  Nette\Application\Route,
  Nette\Application\SimpleRouter,
  Nette\Debug;


// Step 1: Load Nette Framework
// this allows load Nette Framework classes automatically so that
// you don't have to litter your code with 'require' statements
require_once __DIR__ . '/bootstrap-common.php';


Debug::addPanel(new LayoutPanel);

// Step 3: Configure application
// 3a) get and setup a front controller
$application = Environment::getApplication();
//$application->errorPresenter = 'Error';
//$application->catchExceptions = TRUE;

// Set session path
$session = Environment::getSession();
$session->setSavePath(TMP_DIR . '/sessions/');

// 3b) establish database connection
//$application->onStartup[] = 'UserAuthenticator::initialize';


// Step 4: Setup application router
$allowCoolUrl = true;
$router = $application->getRouter();
if($allowCoolUrl) {
/*  $router[] = new Route('index.php', array(
    'presenter' => 'Base:Homepage',
    'action' => 'default',
  ), Route::ONE_WAY);
*/
  $router[] = new Route('<module>/<presenter>/<action>', array(
    'presenter' => 'Base:Homepage',
    'action' => 'default',
  ));
}
$router[] = new SimpleRouter('Base:Sign:welcome', $allowCoolUrl ? Route::ONE_WAY : 0);



// Step 5: Run the application!
$application->run();
