<?php

namespace ActiveEntity;

interface IBehavioralEntity {} // Just for easy detection in reflection


class BehavioralEntity extends Entity implements IBehavioralEntity {
  /**
   * @var array Data storage for behaviours
   */
  public $_behavioralStorage;
  
  public static $_behaviours = false; // Configuration
  public static $_behavioralConfigLoaded = false;
  public static $_behavioralProperties; // List of properties
  public static $_behavioralMethods; // Map: method name -> static class
  public static $_behavioralStaticMethods; // Map: method name -> static class
  
  
  /***************** Magic methods **************/
  
  public function &__get($name) {
    if(static::_hasBehavioralProperty($name)) return $this->_behavioralStorage[$name];
    else return parent::__get($name);
  }
  
  public function __set($name, $value) {
    if(static::_hasBehavioralProperty($name)) $this->_behavioralStorage[$name] = $value;
    else parent::__set($name);
  }
  
  public function __isset($name) {
    return static::_hasBehavioralProperty($name) || parent::__isset($name);
  }
  
  public function __unset($name) {
    if(static::_hasBehavioralProperty($name)) throw new \Exception("Unable to unset behavioral property $name");
    else parent::__unset($name);
  }
  
  public function __call($method, $args) {
    if(static::_hasBehavioralMethod($method)) {
      $cb = array(static::$_behavioralMethods[$method], 'call' . ucfirst($method));
      if(!is_callable($cb, false, $cbOut)) throw new \Exception("Behavioral method '$method' has no callable implementation $cbOut");
      array_unshift($args, get_called_class(), $this);
      return call_user_func_array($cb, $args);
    }
    else return parent::__call($method, $args);
  }
  
  public static function __callStatic($method, $args) {
    if(static::_hasBehavioralStaticMethod($method)) {
      $cb = array(static::$_behavioralStaticMethods[$method], 'callStatic' . ucfirst($method));
      if(!is_callable($cb, false, $cbOut)) throw new \Exception("Behavioral method '$method' has no callable implementation $cbOut");
      array_unshift($args, get_called_class());
      return call_user_func_array($cb, $args);
    }
    else return parent::__callStatic($method, $args);
  }
  
  
  /********** Behavioral implementation ***************/
  
  protected static function _loadBehavioralConfig() {
    if(static::$_behavioralConfigLoaded) return; // Already loaded
    
    if(static::$_behaviours === false) throw new \Exception("Behavioral configuraion not set, please set up static variable \$_behaviours in class " . get_called_class());

    static::$_behavioralProperties = array();
    static::$_behavioralMethods = array();
    static::$_behavioralStaticMethods = array();
    
    // Initialize all behaviours
    $className = get_called_class();
    if(is_array(static::$_behaviours)) foreach(static::$_behaviours as $k => $v) {
      // Decode config item - what is behaviour name and what are arguments
      if(is_numeric($k)) {
        $behav = $v;
        $args = null;
      }
      else {
        $behav = $k;
        $args = $v;
      }
      
      // Call behavioral register
      $behavClassName = static::_formatBehavioralClassName($behav);
      if(!class_exists($behavClassName)) throw new \Exception("Behaviour class '$behavClassName' for behaviour '$behav' not exists");
      if(is_callable(array($behavClassName, 'register'), false)) {
        $behavClassName::register($className, $args);
      }
    }
    
    // Mark as loaded
    static::$_behavioralConfigLoaded = true;
  }
  
  public static function _hasBehavioralProperty($name) {
    static::_loadBehavioralConfig();
    return in_array($name, static::$_behavioralProperties);
  }
  
  public static function _hasBehavioralMethod($name) {
    static::_loadBehavioralConfig();
    return isset(static::$_behavioralMethods[$name]);
  }
  
  public static function _hasBehavioralStaticMethod($name) {
    static::_loadBehavioralConfig();
    return isset(static::$_behavioralStaticMethods[$name]);
  }
  
  /**
   * Convert behaviour name to class name
   */
  public static function _formatBehavioralClassName($name) {
    return "$name";
  }
  
  public static function _setupBehavioralMetadata(\Doctrine\ORM\Mapping\ClassMetadataInfo $metadata) {
    // Initialize all behaviours
    $className = get_called_class();
    if(is_array(static::$_behaviours)) foreach(static::$_behaviours as $k => $v) {
      // Decode config item - what is behaviour name and what are arguments
      if(is_numeric($k)) {
        $behav = $v;
        $args = null;
      }
      else {
        $behav = $k;
        $args = $v;
      }
      
      // Call behavioral register
      $behavClassName = static::_formatBehavioralClassName($behav);
      if(!class_exists($behavClassName)) throw new \Exception("Behaviour class '$behavClassName' for behaviour '$behav' not exists");
      if(is_callable(array($behavClassName, 'setUpMetadata'), false)) {
        $behavClassName::setUpMetadata($metadata, $className, $args);
      }
    }
  }
}

