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
 * @deprecated We should never call this global methods
* Connect to database using Doctrine
*/


/**
 * @return Doctrine\ORM\EntityManager
 */
function em() {
  return Nette\Environment::getService('Doctrine\\ORM\\EntityManager');
}

/**
 * @return Doctrine\ORM\Query
 */
function q() {
  $args = func_get_args();
  return call_user_func_array(array(em(), 'createQuery'), $args);
}

