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
* Zprostredkovani komunikace systemu s Access Pointy
*/


/**
* Access Point Operating System
*/
abstract class APos {
  /**
   * Wrapper for connecting to an APos
   * @param AP|int $ap
   * @return \Thrift\APos\APosIf
   */
  static function connect($ap, $connector = null) {
    if(is_numeric($ap)) {
      $id = $ap;
      if(!$ap = \AP::find($id)) throw new \NotFoundException("AP not found");
    }
    elseif($ap instanceof \AP) {
      // It's OK
    }
    else throw new \InvalidArgumentException("Parameter expected to be an AP");

    return \APos\Connector\Factory::connect($ap, $connector);
  }
}
