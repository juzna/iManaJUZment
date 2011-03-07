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
 
require_once __DIR__ . '/../bootstrap.php';

// Table rows are arrays
$ds = new Juz\Tables\DataSource\DummySource;

Assert::equal($ds->count(), 2);
Assert::equal(count($ds), 2);

$ds->rewind(); // Rewind to start
Assert::true($ds->valid()); // First item must be present
Assert::true(is_array($ds->current()));
$ds->next();

Assert::true($ds->valid()); // Second item must be present
Assert::true(is_array($ds->current()));
$ds->next();

Assert::false($ds->valid()); // No more items
Assert::null($ds->current()); // Must return null

Assert::false($ds[0] instanceof ArrayAccess);
Assert::true(is_array($ds[0]));
Assert::true(is_array($ds[1]));
Assert::null($ds[2]);

Assert::same($ds[1]['interface'], 'ether1');



// Table rows are stdClass objects
$ds = new Juz\Tables\DataSource\DummySource(null, function($item) { return (object) $item; });

Assert::equal($ds->count(), 2);
Assert::false($ds[0] instanceof ArrayAccess);
Assert::true($ds[0] instanceof stdClass);
Assert::true($ds[1] instanceof stdClass);
Assert::null($ds[2]);
Assert::same($ds[1]->interface, 'ether1');




// Table rows are objects with magic methods
class MagicObject {
  private $params;
  function __construct($params) {
    $this->params = (array) $params;
  }

  function __get($name) {
    return $this->params[$name];
  }
}

$ds = new Juz\Tables\DataSource\DummySource(null, function($item) { return new MagicObject($item); });

Assert::equal($ds->count(), 2);
Assert::false($ds[0] instanceof ArrayAccess);
Assert::false($ds[0] instanceof stdClass);
Assert::false($ds[1] instanceof stdClass);
Assert::true($ds[0] instanceof MagicObject);
Assert::true($ds[1] instanceof MagicObject);
Assert::null($ds[2]);
Assert::same($ds[1]->interface, 'ether1');




// Table rows are objects implementing ArrayAccess
class ArrayAccessObject implements ArrayAccess {
  private $params;

  function __construct($params) {
    $this->params = (array) $params;
  }

  public function offsetUnset($offset) { throw new LogicException; }
  public function offsetSet($offset, $value) { throw new LogicException; }
  public function offsetGet($offset) {
    return $this->params[$offset];
  }

  public function offsetExists($offset) {
    return array_key_exists($offset, $this->params);
  }
}

$ds = new Juz\Tables\DataSource\DummySource(null, function($item) { return new ArrayAccessObject($item); });

Assert::equal($ds->count(), 2);
Assert::true($ds[0] instanceof ArrayAccess);
Assert::false($ds[0] instanceof stdClass);
Assert::false($ds[1] instanceof stdClass);
Assert::null($ds[2]);
Assert::same($ds[1]['interface'], 'ether1');
