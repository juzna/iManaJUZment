<?php

namespace ActiveEntity\Behaviours;

class BaseBehaviour {
  public static $cols = null;
  public static $methods = null;
  public static $staticMethods = null;
  
  /**************** Definition methods *****************/
  
  /**
   * Add column to definition
   */
  public static function hasColumn($name, $type, $len = null, $params = null) {
    $params = (array) $params;
    $params['fieldName'] = $name;
    $params['columnName'] = $name;
    $params['type'] = $type;
    $params['length'] = $len;
    $params['nullable'] = true;
    
    self::$cols[get_called_class()][] = $params;
  }
  
  /**
   * Add method to definition
   */
  public static function hasMethod($name, $cb) {
    self::$methods[get_called_class()][$name] = $cb;
  }
  
  public static function hasStaticMethod($name, $cb) {
    self::$staticMethods[get_called_class()][$name] = $cb;
  }
  
  
  
  
  /**
   * Definition of behaviour, should be overriden
   */
  public static function setDefinition($className, $args) {
    throw new \Exception("Not implemented, shoud be overriden");
  }
  
  /**
   * Get list of columns
   */
  public static function getColumns() {
    return self::$cols[get_called_class()];
  }
  
  public static function getMethods() {
    return self::$methods[get_called_class()];
  }
    
  public static function getStaticMethods() {
    return self::$staticMethods[get_called_class()];
  }
  
  /**
   * Wrapper for setting up definition
   */
  public static function _setDefinition($className) {
    if(isset(static::$cols[get_called_class()])) return;
    
    static::$cols[get_called_class()] = array();
    static::$methods[get_called_class()] = array();
    static::$staticMethods[get_called_class()] = array();
    
    $args = $className::_getBehavioralConfig(get_called_class());
    static::setDefinition($className, $args);
  }

  /**
   * Register this behaviour to class
   */
  public static function register($className, $args) {
    static::_setDefinition($className);

    // Map fields
    foreach(static::getColumns() as $mapping) $className::_addbehavioralProperty($mapping['fieldName'], $mapping);
    foreach(static::getMethods() as $name => $cb) $className::_addBehavioralMethod($name, $cb);
    foreach(static::getStaticMethods() as $name => $cb) $className::_addBehavioralStaticMethod($name, $cb);
  }
  
  /**
   * Set-up metadata for class
   */
  public static function setUpMetadata(\Doctrine\ORM\Mapping\ClassMetadataInfo $metadata, $className, $params) {
    static::_setDefinition($className);
    foreach(static::getColumns() as $mapping) $metadata->mapField($mapping);
  }
}

