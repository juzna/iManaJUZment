<?php
namespace ActiveEntity\Reflection;


class ReflectionClass extends \ReflectionClass {
  public function getProperty($name) {
    $cls = $this->name;
    if($this->implementsInterface('ActiveEntity\IBehavioralEntity') && $cls::_hasBehavioralProperty($name)) return new BehaviourProperty($this->name, $name);
    else return new ReflectionProperty($this->name, $name);
  }
}

