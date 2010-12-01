<?php

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

