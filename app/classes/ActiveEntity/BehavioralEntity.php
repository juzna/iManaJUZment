<?php

namespace ActiveEntity;

interface IBehavioralEntity {} // Just for easy detection in reflection


class BehavioralEntity extends Entity implements IBehavioralEntity {
  /**
   * @var array Data storage for behaviours, per instance
   */
  public $_behavioralStorage;
  
  public static $_behaviours = false; // Configuration, per class (i.e. has to use static::)
  private static $_behavioralConfigLoaded = array(); // Map: class -> bool
  private static $_behavioralProperties; // Map: class -> (property name -> params)
  private static $_behavioralMethods; // Map: class -> (method name -> static class)
  private static $_behavioralStaticMethods; // Map: class -> (method name -> static class)
  private static $_behavioralClasses; // Map: class -> (array of behavioral classes)
  private static $_behavioralConfig; // Map: class -> (behav -> hashmap)
  
  
  /***************** Magic methods **************/
  
  public function __construct() {
    static::_loadBehavioralConfig();
    self::getEntityManager()->getEventManager()->dispatchEvent('create', new \Doctrine\ORM\Event\LifeCycleEventArgs($this, self::getEntityManager()));
  }
  
  public function &__get($name) {
    if(static::_hasBehavioralProperty($name)) return $this->_behavioralStorage[$name];
    else return parent::__get($name);
  }
  
  public function __set($name, $value) {
    if(static::_hasBehavioralProperty($name)) $this->_behavioralStorage[$name] = $value;
    else parent::__set($name, $value);
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
      $cb = self::$_behavioralMethods[get_class($this)][$method];
      if(!is_callable($cb, false, $cbOut)) throw new \Exception("Behavioral method '$method' has no callable implementation $cbOut");
      array_unshift($args, get_called_class(), $this);
      return call_user_func_array($cb, $args);
    }
    
    // Setter/getter for behavioral property
    elseif(preg_match('/^(set|get|is)([A-Z][A-Za-z0-9_]*)$/', $method, $match) && static::_hasBehavioralProperty(lcfirst($match[2]))) {
      $name = lcfirst($match[2]);
      switch($match[1]) {
        case 'get':
        case 'is':
          if(sizeof($args) != 0) throw new \Exception("Expected not arguments");
          return $this->_behavioralStorage[$name];
          
        case 'set':
          if(sizeof($args) != 1) throw new \Exception("Expected one argument");
          $this->_behavioralStorage[$name] = $args[0];
          return $this; // Supports fluent interface
      }
    }
    
    else return parent::__call($method, $args);
  }
  
  public static function __callStatic($method, $args) {
    if(static::_hasBehavioralStaticMethod($method)) {
      $cb = self::$_behavioralStaticMethods[get_called_class()][$method];
      if(!is_callable($cb, false, $cbOut)) throw new \Exception("Behavioral method '$method' has no callable implementation $cbOut");
      array_unshift($args, get_called_class());
      return call_user_func_array($cb, $args);
    }
    else return parent::__callStatic($method, $args);
  }
  
  /********** Manipulating with behaviours **************/

  /**
   * Add new property
   * @param string $name Column name
   * @param array $params Metadata parameters
   */
  public static function _addBehavioralProperty($name, $params = null) {
    self::$_behavioralProperties[get_called_class()][$name] = $params;
  }
  
  public static function _addBehavioralMethod($name, $cb) {
    self::$_behavioralMethods[get_called_class()][$name] = $cb;
  }

  public static function _addBehavioralStaticMethod($name, $cb) {
    self::$_behavioralStaticMethods[get_called_class()][$name] = $cb;
  }
  
  
  /********** Behavioral implementation ***************/
  
  protected static function _loadBehavioralConfig() {
    $cc = get_called_class();
    if(isset(self::$_behavioralConfigLoaded[$cc])) return; // Already loaded
    
    if(static::$_behaviours === false) throw new \Exception("Behavioral configuraion not set, please set up static variable \$_behaviours in class " . get_called_class());

    self::$_behavioralProperties[$cc] = array();
    self::$_behavioralMethods[$cc] = array();
    self::$_behavioralStaticMethods[$cc] = array();
    
    // Initialize all behaviours
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
      
      // Set default config
      self::$_behavioralConfig[$cc][$behav] = $args;
      
      // Call behavioral register
      $behavClassName = static::_formatBehavioralClassName($behav);
      if(!class_exists($behavClassName)) throw new \Exception("Behaviour class '$behavClassName' for behaviour '$behav' not exists");
      self::$_behavioralClasses[$cc][] = $behavClassName; // Add to known behaviours of this class
      if(is_callable(array($behavClassName, 'register'), false)) {
        $behavClassName::register($cc, $args);
      }
    }
    
    // Mark as loaded
    self::$_behavioralConfigLoaded[$cc] = true;
  }
  
  public static function _hasBehavioralProperty($name) {
    static::_loadBehavioralConfig();
    return isset(self::$_behavioralProperties[get_called_class()][$name]);
  }
  
  public static function _hasBehavioralMethod($name) {
    static::_loadBehavioralConfig();
    return isset(self::$_behavioralMethods[get_called_class()][$name]);
  }
  
  public static function _hasBehavioralStaticMethod($name) {
    static::_loadBehavioralConfig();
    return isset(self::$_behavioralStaticMethods[get_called_class()][$name]);
  }
  
  public static function _hasBehaviour($className) {
    static::_loadBehavioralConfig();
    return in_array($className, self::$_behavioralClasses[get_called_class()]);
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
  
  /***************** Config variables *****************/
  
  public static function _getBehavioralConfigVar($behav, $name) {
    return self::$_behavioralConfig[get_called_class()][$behav][$name];
  }
  
  public static function _setBehavioralConfigVar($behav, $name, $value) {
    self::$_behavioralConfig[get_called_class()][$behav][$name] = $value;
  }
  
  public static function &_getBehavioralConfig($behav) {
    return self::$_behavioralConfig[get_called_class()][$behav];
  }
}

