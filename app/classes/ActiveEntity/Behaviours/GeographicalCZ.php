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


namespace ActiveEntity\Behaviours;

class GeographicalCZ extends BaseBehaviour{
  public static function setDefinition($className, $args) {
    self::hasColumn('posX', 'integer', 11, array());
    self::hasColumn('posY', 'integer', 11, array());

    // Postal address
    self::hasColumn('ulice', 'string', 100, array());
    self::hasColumn('cisloPopisne', 'string', 100, array());
    self::hasColumn('mesto', 'string', 100, array());
    self::hasColumn('PSC', 'string', 10, array());
    self::hasColumn('stat', 'string', 10, array());

    self::hasColumn('uir_obec', 'integer', 11, array());
    self::hasColumn('uir_cobce', 'integer', 11, array());
    self::hasColumn('uir_ulice', 'integer', 11, array());
    self::hasColumn('uir_objekt', 'integer', 11, array());
    self::hasColumn('uir_special', 'boolean', null, array()); // Specialni adresa (neni z UIR)
  }
}

