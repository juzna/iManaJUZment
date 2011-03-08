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

use Juz\Tables\Field;

// Initialize definition
$def = new Juz\Tables\Definition\DoctrineDefinition('AP');

Assert::same($def->getId(), 'doctrine_AP');
Assert::same($def->getTitle(), 'Access points');
Assert::same($def->getFieldIndex(), 'ID');
Assert::same($def->getSortFields(), array(array('ID', \Juz\Tables\ISortable::ORDER_ASCENDING)));

// Test fields
$fields = $def->getFields();
$mustHaveKeys = array('ID', 'name', 'description');
foreach($mustHaveKeys as $key) Assert::true($fields[$key] instanceof Field);

Assert::same($fields['ID']->name, 'ID');
Assert::same($fields['ID']->title, 'ID');
Assert::same($fields['ID']->variable, 'ID');


// Created field
/** @var $field Juz\Tables\Field */
$field = $fields['created'];
Assert::true($field instanceof Field);
Assert::same($field->helper, 'date');



// Header links
{
  $links = $def->getHeaderLinks();
  Assert::same(count($links), 1);

  /** @var $link ActiveEntity\LinkMetadata */
  $link = $links[0];
  Assert::true($link instanceof ActiveEntity\LinkMetadata);
  Assert::same($link->title, 'Add');
  Assert::same($link->params[0], 'ap');
}


// Item links
{
  $links = $def->getItemLinks();
  Assert::same(count($links), 3);

  /** @var $link ActiveEntity\LinkMetadata  Detail link*/
  $link = $links[0];
  Assert::true($link instanceof ActiveEntity\LinkMetadata);
  Assert::same($link->title, 'detail');
  Assert::same($link->params, array('$ID'));

  /** @var $link ActiveEntity\LinkMetadata  Edit link*/
  $link = $links[1];
  Assert::true($link instanceof ActiveEntity\LinkMetadata);
  Assert::same($link->title, 'edit');
  Assert::same($link->params, array('ap', '$ID'));
}
