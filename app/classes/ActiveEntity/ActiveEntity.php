<?php

namespace ActiveEntity;
use Doctrine\ORM\EntityManager;

abstract class Entity extends \Nette\Object implements \ArrayAccess {
  /**
   * @var EntityManager Static entity manager
   */
  private static $entityManager;
  
  
  
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
  private static function getEntityManager() {
      if (!self::$entityManager === null) {
          throw Exception::noEntityManager();
      }
      return self::$entityManager;
  }



  /************ Persistance ***************/
  
  public function save() {
    return self::getEntityManager()->persist($this);
  }

  public function delete() {
    return self::getEntityManager()->remove($this);
  }
  
  public function detach() {
    return self::getEntityManager()->detach($this);
  }
  
  public function merge() {
    return self::getEntityManager()->merge($this);
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

  public static function getClassMetadata() {
    return self::getEntityManager()->getClassMetadata(get_called_class());
  }
  
  public function &__get($name) {
    if(property_exists($this, $name)) return $this->$name;
    else throw new \Exception("Property $name not exists");
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
}

