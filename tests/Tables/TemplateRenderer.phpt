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

// Dummy presenter to support links
class DummyPresenter {
  function link($target, $params) {
    return $target . '&' . implode(', ', $params);
  }
}
$presenter = new DummyPresenter;

// Allows to create renderers with different datasource
function createRenderer($ds) {
  global $presenter;

  $def = new Juz\Tables\Definition\DummyDefinition;

  $renderer = new Juz\Tables\Creator\TemplateRenderer(array('presenter' => $presenter));
  $renderer->setTableDefinition($def);
  $renderer->setDataSource($ds);

  return $renderer;
}


// Sample outputs
if(@$_SERVER['argv'][1] === 'sample') {
  $ds = new Juz\Tables\DataSource\DummySource;
  $renderer = createRenderer($ds);

  switch(@$_SERVER['argv'][2]) {
    case null:
    default:
      echo 'Supported samples: code, output';
      break;

    case 'code':
      echo $renderer->generateTemplateCode();
      break;

    case 'output':
      echo $renderer->toString();
      break;
  }
  exit;
}

// Test output
//Assert::same($renderer->toString(), file_get_contents(__FILE__ . '.output'));


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
// (so far this test fails, because TemplateRenderer is not ready for it)
// TODO: fix template renderer and allow this test
if(false) {
  $ds = new Juz\Tables\DataSource\DummySource(null, function($item) { return new ArrayAccessObject($item); });
  $renderer = createRenderer($ds);

  // Test output
  Assert::same($renderer->toString(), file_get_contents(__FILE__ . '.output'));
}
