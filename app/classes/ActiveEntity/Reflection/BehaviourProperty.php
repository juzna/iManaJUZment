<?php

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
    return $entity->_behavioralStorage[$this->name];
  }
}

