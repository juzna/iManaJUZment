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

/** @var $md Juz\ClassMetaData */
$md = ActiveEntity\Entity::getClassMetadata('AP');

/** @var $ae \ActiveEntity\Metadata */
$ae = $md->getExtension('ActiveEntity');

Assert::true($ae instanceof ActiveEntity\Metadata);
Assert::same($ae->getTitle(), 'Access points');

Assert::true(!!$ae->getFieldAnnotations('network', 'Required'));
Assert::true(!!$ae->getFieldAnnotations('network', 'Immutable'));

Assert::same($ae->getNameField(), 'name');
Assert::same($ae->getSortFields(), array(array('name', 1)));

// Required fields
Assert::same($ae->getRequiredFields(), array('network'));


// Header links
$links = $ae->getHeaderLinks();
Assert::equal(count($links), 1); // Just one link
Assert::true($links[0] instanceof ActiveEntity\LinkMetadata);
Assert::same($links[0]->title, 'Add');
Assert::same($links[0]->module, 'AP');
Assert::same($links[0]->view, 'add');



// Item links
$links = $ae->getItemLinks();
Assert::equal(count($links), 3); // detail, edit, clone
Assert::same($links[0]->title, 'detail');
foreach($links as $link) Assert::true($link instanceof ActiveEntity\LinkMetadata);