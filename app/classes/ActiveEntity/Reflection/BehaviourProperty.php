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

class BehaviourProperty {
  public $class;
  public $name;
  
  public function __construct($class, $name) {
    $this->class = $class;
    $this->name = $name;
  }

  public function setAccessible($flag) {}

  public function setValue($entity = null, $value = null) {
    $entity->_behavioralStorage[$this->name] = $value;
  }

  public function getValue($entity = null) {
    return isset($entity->_behavioralStorage[$this->name]) ? $entity->_behavioralStorage[$this->name] : null;
  }
}

