<?php
/**
 * This file is part of the "iManaJUZment" - complex system for internet service providers
 *
 * Copyright (c) 2005 - 2011 Jan Dolecek (http://juzna.cz)

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

// Get annotation driver
/** @var $driver Juz\AnnotationDriver */
$driver = Nette\Environment::getService('Doctrine\ORM\Mapping\Driver\AnnotationDriver');


/**
 * Class for testing purposes
 *
 * @ae:behaviour @ae:editable @ae:title("Access points", single="Access Point")
 * @frm:Override @frm:EntitySelect @ahoj:nic
 */
class AnnotationReaderTestEntity {
  /**
   * @var string
   * @ae:required @ae:title("Some title") @frm:override @ahoj:something
   */
  protected $field1;

  protected $field2;

  /**
   * @var int
   * @frm:override
   */
  protected $field3;
}


// Load metadata
list($classMetadata, $fieldMetadata) = $x = $driver->getExtensionMetadata(new ReflectionClass('AnnotationReaderTestEntity'));

// Test if all extensions are present
Assert::same(array('ActiveEntity', 'Forms'), array_keys($classMetadata));

// Test active-entity parameters
Assert::true(in_array('Behaviour', array_keys($classMetadata['ActiveEntity'])));
Assert::true(in_array('Editable', array_keys($classMetadata['ActiveEntity'])));
Assert::true(in_array('Title', array_keys($classMetadata['ActiveEntity'])));

// Test field parameters
Assert::true(isset($fieldMetadata['ActiveEntity']['field1']['Required']));
Assert::same($fieldMetadata['ActiveEntity']['field1']['Title']->value, 'Some title');
Assert::true(!isset($fieldMetadata['ActiveEntity']['field2']));
Assert::true(isset($fieldMetadata['Forms']['field3']['Override']));

