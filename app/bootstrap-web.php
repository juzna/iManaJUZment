<?php
/**
 * My Application bootstrap file.
 *
 * @copyright  Copyright (c) 2009 John Doe
 * @package    MyApplication
 */

use Nette\Environment;
use Nette\Application\Route;
use Nette\Application\SimpleRouter;


// Step 1: Load Nette Framework
// this allows load Nette Framework classes automatically so that
// you don't have to litter your code with 'require' statements
require_once __DIR__ . '/bootstrap-common.php';




// Step 3: Configure application
// 3a) get and setup a front controller
$application = Environment::getApplication();
$application->errorPresenter = 'Error';
//$application->catchExceptions = TRUE;


// 3b) establish database connection
$application->onStartup[] = 'UsersModel::initialize';


// Step 4: Setup application router
$router = $application->getRouter();
/*
$router[] = new Route('index.php', array(
	'presenter' => 'Base:Homepage',
	'action' => 'default',
), Route::ONE_WAY);

$router[] = new Route('<presenter>/<action>/<id>', array(
	'presenter' => 'Base:Homepage',
	'action' => 'default',
	'id' => NULL,
));
*/
$router[] = new SimpleRouter('Base:Dashboard:default');


// Step 5: Run the application!
$application->run();

