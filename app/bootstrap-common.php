<?php
/**
 * This file is part of the "iManaJUZment" - complex system for internet service providers
 *
 * Copyright (c) 2005 - 2011 Jan Dolecek (http://juzna.cz)
 *
 * iManaJUZment is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with iManaJUZment.  If not, see <http://www.gnu.org/licenses/gpl.txt>.
 *
 * @license http://www.gnu.org/licenses/gpl.txt
 */

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

