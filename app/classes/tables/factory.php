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


namespace Tables;

class DataSourceFactory {
  public static $implementations = array(
    'd:table'   => 'doctrineTable',
    'd:model'   => 'doctrineModel',
    'd:dql'     => 'doctrineDQL',
    'dql'       => 'doctrineDQL',
    'sql'       => 'dibi',
  );
  
  public function __construct() {
    throw new \Exception("Is static class");
  }
  
  public static function addImpl($name, $callback) {
    self::$implementations[$name] = $callback;
  }
  
  /**
   * Creates data source defined by parameters
   * @param string $type Type of DataSource to be used
   * @param array $definition Definition parameters
   * @param array $variables List of arguments for generating the source
   * @return Traversable
   */
  public static function create($type, $definition, $variables = null) {
    if(!isset(self::$implementations[$type])) throw new \Exception("Data source type is unknown: '$type'");
    
    $cb = self::$implementations[$type];
    if(is_string($cb) && is_callable($cb2 = "\\Tables\\DataSourceFactory::$cb")) $cb = $cb2;
    if(!is_callable($cb)) throw new \Exception("Data source is not callable: '$cb'");
    
    return call_user_func($cb, $definition, $variables);
  }
  
  /**
   * Get data source from table definition
   * @param ITableDefinition $def
   * @return Traversable
   */
  public static function fromTableDefinition(ITableDefinition $def, $variables = null) {
    // Det DS from definition
    $ds = $def->getDataSource();
    
    // array -> assume it's params for creator
    if(is_array($ds)) {
      $dsDef = $ds;
      $dsType = $dsDef['type'];
      return DataSourceFactory::create($dsType, $dsDef, $variables);
    }
    
    // Traversable -> assume it's datasource already
    elseif($ds instanceof Traversable) {
      return $ds;
    }
    
    else throw new TableException("Data source is unknown: $ds");
  }
  
  
  
  /**************** creators ***********************/
  
  /**
   * Creates data source for Doctrine table
   */
  public static function doctrineTable($args, $variables = null) {
    return em()->getRepository($args['value'])->findAll();
  }
  
  public static function doctrineModel($args, $params) {
    $model = @$args['model'];
    $id = @$args['id'];
    $property = @$args['property'];
    if(empty($model) || empty($id) || empty($property)) throw new \Exception("Missing required parameters: model, id, property");
    
    // Get Index of entity from arguments
    if(!isset($params[$id])) throw new \Exception("Missing index variable: '$id'");
    $index = $params[$id];
    
    $item = em()->find($model, $index);
    if(!$item) throw new \NotFoundException("Entity '$model' with ID '$id' not found");
    
    return $item->$property;
  }
  
  /**
   * Get data source from DQL
   */
  public static function doctrineDQL($args, $params = null) {
    
  }
  
  /**
   * Creates data source for SQL query using dibi
   */
  public static function dibi($args) {
    return \dibi::query($args['value']);
  }
}
