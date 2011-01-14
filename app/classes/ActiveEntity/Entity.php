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


namespace ActiveEntity;
use Doctrine\ORM\EntityManager;

abstract class Entity extends \Nette\Object implements \ArrayAccess {
  /**
   * @var EntityManager Static entity manager
   */
  private static $entityManager;
  
  public function __construct() {}
  
  
  /************ Entity Manager configuration ***************/
  
  /**
   * Configures static entity manager for all entities
   * @param EntityManager $em
   */
  public static function setEntityManager(EntityManager $em) {
      self::$entityManager = $em;
  }

  /**
   * Finds static entity manager to be used by default
   * @return EntityManager
   */
  public static function getEntityManager() {
      if (!self::$entityManager === null) {
          throw Exception::noEntityManager();
      }
      return self::$entityManager;
  }



  /************ Persistance ***************/

  public static function find($id, $className = null) {
    if(!isset($className)) $className = get_called_class();
    return self::getEntityManager()->getRepository($className)->find($id);
  }

  /**
   * Gets Doctrine's repository of a class
   * @param string $className
   * @return EntityRepository
   */
  public static function getRepository($className = null) {
    if(!isset($className)) $className = get_called_class();
    return self::getEntityManager()->getRepository($className);
  }
  
  public function persist() {
    return self::getEntityManager()->persist($this);
  }

  public function remove() {
    return self::getEntityManager()->remove($this);
  }
  
  public function detach() {
    return self::getEntityManager()->detach($this);
  }
  
  public function merge() {
    return self::getEntityManager()->merge($this);
  }
  
  public function flush() {
    return self::getEntityManager()->flush();
  }
  
  public function getState() {
    return self::getEntityManager()->getUnitOfWork()->getEntityState($this);
  }
  
  

  /************ ArrayAccess methods ***************/
  
  final public function offsetExists($key) {
    return property_exists($this, $key);
  }

  final public function offsetGet($key) {
    return $this->__get($key);
  }

  final public function offsetSet($key, $value) {
      $this->__set($key, $value);
  }

  final public function offsetUnset($key) {
    $this->__unset($key);
  }  
  
  
  /************ Magic methods ***************/
  
  public function &__get($name) {
    if(property_exists($this, $name)) return $this->$name;
    else throw new \Exception("Property $name of class " . get_class($this) . " not exists");
  }
  
  public function __set($name, $value) {
    if(property_exists($this, $name)) $this->$name = $value;
    else throw new \Exception("Property $name not " . get_class($this) . "exists");
  }  
  
  /**
   * Converts entity to array
   * @return array
   */
  public function toArray($withRelations = false) {
    $ret = array();
    foreach(self::getClassMetadata()->getFieldDefinitions() as $fieldName => $def) {
      if(isset($def)) $ret[$fieldName] = $this->$fieldName;
      elseif($withRelations && $this->$fieldName instanceof \Doctrine\ORM\Proxy\Proxy) {
        $prop = new \ReflectionProperty($this->$fieldName, '_identifier');
        $prop->setAccessible(true);
        $x = $prop->getValue($this->$fieldName);
        $ret[$fieldName] = $x['ID'];
      }
    }
    return $ret;
  }
  
  public static function fromArray($arr) {
    $cls = get_called_class();
    $fields = self::getFieldNames($cls);
    $obj = new $cls;

    foreach($arr as $k => $v) {
      if(in_array($k, $fields)) $obj->$k = $v;
    }

    return $obj;
  }

  /**
   * Map static calls to repository
   */
  public static function __callStatic($method, $arguments) {
    return call_user_func_array(
        array(self::getEntityManager()->getRepository(get_called_class()), $method),
        $arguments
    );
  }
  
  public function __toString() {
    return get_class($this) . '@' . $this->ID;
  }
  
  
  /***************** Metadata methods ****************/
  
  /**
   * Get's medatada describing this class
   * @return ActiveEntity\ClassMetadata
   */
  public static function getClassMetadata($className = null) {
    return self::getEntityManager()->getClassMetadata(isset($className) ? self::_sanitizeClassName($className) : get_called_class());
  }
  
  /**
   * Get list of field names
   * @return array
   */
  public static function getFieldNames($className = null) {
    return self::getClassMetadata($className)->getFieldNames();
  }
  
  public static function getFieldDefinitions($className = null) {
    return self::getClassMetadata($className)->getFieldDefinitions();
  }


  /******************   Special fetching    *****************/

  public static function fetchPairs($key, $val, $className = null) {
    $className = isset($className) ? $className : get_called_class();

    return self::getEntityManager()->createQueryBuilder()->
      select("partial i.{" . "$key, $val}")->from($className, 'i')->
      getQuery()->getPairsResult('i_' . $key, 'i_' . $val);
  }

  /**
   * Get real name of a class. If proxy given, convert to real class
   * @throws InvalidArgumentException
   * @param string $cls
   * @return string
   */
  public static function _sanitizeClassName($cls) {
    if(is_object($cls)) $cls = get_class($cls);

    if($cls instanceof Proxy) {
      if(preg_match('/^Proxy\\\\(.+)Proxy$/', $cls, $match)) {
        $cls = $match[1];
      }
      else throw new \InvalidArgumentException("Dont understand class $cls");
    }

    return $cls;
  }
}

