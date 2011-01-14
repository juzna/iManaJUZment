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


namespace ActiveEntity\Reflection;

class ReflectionProperty extends \ReflectionProperty {
  public function __construct($class, $name) {
    parent::__construct($class, $name);
  }

  public function setAccessible($flag) {
    return parent::setAccessible($flag);
  }

  public function setValue($entity = null, $value = null) {
    return parent::setValue($entity, $value);
//    $entity->set($this->name, $value);
  }

  public function getValue($entity = null) {
    return parent::getValue($entity);
//      return $entity->get($this->name);
  }
}

