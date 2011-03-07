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

namespace Juz\Tables\DataSource;

class DummySource extends ArraySource implements \Juz\Tables\IDataSource {
  public static $items = array(
    // First default item
    array(
      'ID'        => 10,
      'interface' => 'ether1',
      'ip'        => '192.168.10.1',
      'netmask'   => '24',
    ),

    // Second default item
    array(
      'ID'        => 11,
      'interface' => 'ether1',
      'ip'        => '192.168.11.1',
      'netmask'   => '24',
    ),
  );

  public function __construct() {
    parent::__construct(static::$items);
  }
}
