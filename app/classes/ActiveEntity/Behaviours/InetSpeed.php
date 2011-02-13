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

class InetSpeed extends Basebehaviour {
  public static function setDefinition($className, $args) {
  	// Inet speeds
  	$fields = array('min', 'max', 'burst', 'tresh', 'time');
  	$types = array('tx', 'rx');
  	foreach($fields as $field) {
  	  foreach($types as $type) self::hasColumn($type . $field, 'string', 20,
        // Params
        array(

        ),

        // Metadata
        array(
          'ActiveEntity\Annotations\Title' => new \ActiveEntity\Annotations\Title(array('value' => ucfirst($field) . ' ' . $type . ' speed'))
        )
      );
  	}
  	
  	self::hasColumn('txpriority', 'integer', 2, array());
  	self::hasColumn('rxpriority', 'integer', 2, array());
  }
}

