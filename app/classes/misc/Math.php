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
* Common mathematic functions
*/
class Math {
  /**
  * Derivuje pole
  */
  function derive($arr, $first = false) {
	  $ret = array();

	  // Posledni polozka
	  $last = $first === false ? array_shift($arr) : $first;

	  $len = sizeof($arr);
	  while($len-- > 0) {
		  $x = array_shift($arr);
		  $ret[] = $x - $last;

		  $last = $x;
	  }

	  return $ret;
  }
}

