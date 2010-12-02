<?php

namespace ActiveEntity\Behaviours;

class BaseBehaviour {
  public static $cols = null;
  
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
   * Definition of behaviour, should be overriden
   */
  public static function setDefinition() {
    throw new \Exception("Not implemented, shoud be overriden");
  }
  
  /**
   * Get list of columns
   */
  public static function getColumns() {
    return self::$cols[get_called_class()];
  }
  
  /**
   * Wrapper for setting up definition
   */
  public static function _setDefinition() {
    if(isset(static::$cols[get_called_class()])) return;
    static::$cols[get_called_class()] = array();
    static::setDefinition();
  }

  /**
   * Register this behaviour to class
   */
  public static function register($className) {
    static::_setDefinition();

    // Map fields
    foreach(static::getColumns() as $mapping) $className::_addbehavioralProperty($mapping['fieldName'], $mapping);

    // TODO: map methods and static methods
  }
  
  /**
   * Set-up metadata for class
   */
  public static function setUpMetadata(\Doctrine\ORM\Mapping\ClassMetadataInfo $metadata, $className, $params) {
    static::_setDefinition();
    foreach(static::getColumns() as $mapping) $metadata->mapField($mapping);
  }
}

