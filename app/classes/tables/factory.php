<?php

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
   * @param array $params List of arguments for generating the source
   * @return Traversable
   */
  public static function create($type, $definition, $params = null) {
    if(!isset(self::$implementations[$type])) throw new \Exception("Data source type is unknown: '$type'");
    
    $cb = self::$implementations[$type];
    if(is_string($cb) && is_callable($cb2 = "\\Tables\\DataSourceFactory::$cb")) $cb = $cb2;
    if(!is_callable($cb)) throw new \Exception("Data source is not callable: '$cb'");
    
    return call_user_func($cb, $definition, $params);
  }
  
  /**
   * Creates data source for Doctrine table
   */
  public static function doctrineTable($args, $params = null) {
    return \Doctrine::getTable($args['value'])->findAll();
  }
  
  public static function doctrineModel($args, $params) {
    $model = @$args['model'];
    $id = @$args['id'];
    $property = @$args['property'];
    if(empty($model) || empty($id) || empty($property)) throw new \Exception("Missing required parameters: model, id, property");
    
    // Get Index of entity from arguments
    if(!isset($params[$id])) throw new \Exception("Missing index variable: '$id'");
    $index = $params[$id];
    
    $item = \Doctrine::getTable($model)->find($index);
    if(!$item) throw new \NotFoundException("Entity '$model' with ID '$id' not found");
    
    return $item->$property;
  }
  
  /**
   * Creates data source for SQL query using dibi
   */
  public static function dibi($args) {
    return \dibi::query($args['value']);
  }
}
