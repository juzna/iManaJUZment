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

function createRenderer($ds) {
  $def = new Juz\Tables\Definition\DummyDefinition;

  $renderer = new Juz\Tables\Creator\SimpleHtmlRenderer;
  $renderer->setTableDefinition($def);
  $renderer->setDataSource($ds);

  return $renderer;
}

// Reference output
// $ds = new Juz\Tables\DataSource\DummySource;
// echo $renderer->toString();
// exit;


// Include tests for Array data source (cuz it includes some good classes)
require_once __DIR__ . '/ArraySource.phpt';


// Test data source with array rows
{
  $ds = new Juz\Tables\DataSource\DummySource;
  $renderer = createRenderer($ds);

  // Test output
  Assert::same($renderer->toString(), file_get_contents(__FILE__ . '.output'));
}


// Test data source with stdClass rows
{
  $ds = new Juz\Tables\DataSource\DummySource(null, function($item) { return (object) $item; });
  $renderer = createRenderer($ds);

  // Test output
  Assert::same($renderer->toString(), file_get_contents(__FILE__ . '.output'));    
}


// Table rows are objects with magic methods
{
  $ds = new Juz\Tables\DataSource\DummySource(null, function($item) { return new MagicObject($item); });
  $renderer = createRenderer($ds);

  // Test output
  Assert::same($renderer->toString(), file_get_contents(__FILE__ . '.output'));
}


// Table rows are objects implementing ArrayAccess
{
  $ds = new Juz\Tables\DataSource\DummySource(null, function($item) { return new ArrayAccessObject($item); });
  $renderer = createRenderer($ds);

  // Test output
  Assert::same($renderer->toString(), file_get_contents(__FILE__ . '.output'));
}
