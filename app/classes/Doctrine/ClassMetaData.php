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

namespace Juz;

use Nette\Environment;
use ActiveEntity\Reflection\ReflectionClass,
  ActiveEntity\Reflection\ReflectionProperty;


/**
 * Extensible ClassMetadata mapping driver for Doctrine 2 ORM
 *
 * Extensions are loaded from config.ini based on lines like:
 *   metadata.extension.EXTENSION-NAME = FACTORY
 *
 *   Where extension name can be any string and factory is either:
 *    - class name: then new $extension($className, $classMetadata) is called when extension is needed
 *    - callback: then callback($className, $classMetadata) is called when extension is needed
 *   In these cases, $className is a string representing class name of entity, $classMetadata is object in this file.
 *
 * Programmer can access extensions via $metadata->getExtension(NAME), or using array access like $metadata[NAME]
 *
 * Magic methods are also allowed and directly mapped to extensions. When extension wants to support this feature,
 *   it has to implement IExtensionSubscriber interface and it's one method (which translated method name to callback).
 *
 *
 * @author Jan Dolecek - juzna.cz
 *
 */
class ClassMetaData extends \Doctrine\ORM\Mapping\ClassMetadata implements \ArrayAccess {
  /**
   * Map of extensions: alias -> object
   */
  protected $extensions = array();

  // Cache for implicit extension calls (see __call method)
  protected $callHashMap = array();

  /**
   * Get an extension
   * @param string $name Extension name
   * @return object
   */
  public function getExtension($name) {
    if(!isset($this->extensions[$name])) $this->findAndRegisterExtension($name);
    return $this->extensions[$name];
  }

  /**
   * Add new extension
   * @param string $name Extension name
   * @param string|object $impl Implementation or factory name
   * @return ClassMetaData Provides fluent interface
   */
  public function addExtension($name, $impl) {
    if(isset($this->extensions[$name])) throw new \InvalidArgumentException('Extension with this name already exists');
    $this->extensions[$name] = $impl;
    return $this;
  }

  /**
   * Tries to find an extension by it's name
   * @param string $name Extension name
   * @return bool
   */
  public function findExtension($name) {
    if(!$md = Environment::getConfig('metadata')) return false; //No metadata found in config

    if($md->extension and $factory = $md->extension[$name]) return $this->createExtension($factory);
    elseif($md->alias and $alias = $md->alias[$name]) {
      if($md->extension and $factory = $md->extension[$alias]) return $this->createExtension($factory);
    }

    return false;
  }

  /**
   * Creates an extension by it's factory name
   * @param string $factory Callback or class name
   */
  protected function createExtension($factory) {
    $className = $this->getReflectionClass()->getName();

    if(strpos($factory, ':')) return callback($factory)->invoke($className, $this);
    else return new $factory($className, $this);
  }

  /**
   * Finds extension by it's name and registers it
   */
  public function findAndRegisterExtension($name) {
    $impl = $this->findExtension($name);
    if(!$impl) throw new \NotFoundException('Extension not found: ' . $name);

    $this->addExtension($name, $impl);
  }

  /**
   * Offset to unset
   * @return void
   */
  public function offsetUnset($offset) {
    throw new \InvalidStateException('Cannot removed extension from ClassMetadata');
  }

  /**
   * Another way to add extensions
   * @return void
   */
  public function offsetSet($offset, $value) {
    $this->addExtension($offset, $value);
  }

  /**
   * Simple way to get extensions
   * @return object
   */
  public function offsetGet($offset) {
    return $this->getExtension($offset);
  }

  /**
   * Check if extension exists
   */
  public function offsetExists($name) {
    return isset($this->extensions[$name]) ?: (bool) $this->findExtension($name);
  }

  /**
   * Call to undefined method -> try dispatch it to an extension
   * @param string $method
   * @param array $args
   * @return mixed
   */
  public function __call($method, $args) {
    // Try to get callback from cache
    if(isset($this->callHashMap[$method])) return callback($this->callHashMap[$method])->invokeArgs($args);

    // Find it in extensions
    return $this->__call2($method, $args, true);
  }

  /**
   * Call to undefined method -> try dispatch it to an extension
   * @param string $method
   * @param array $args
   * @param bool $allowAutoLoading Allows autoloading of extensions
   * @return mixed
   */
  protected function __call2($method, $args, $allowAutoLoading = true) {
    // Try all extensions
    foreach($this->extensions as $ext) {
      if($ext instanceof IExtensionSubscriber && $cb = $ext->loader($method)) {
        $this->callHashMap[$method] = $cb; // Store callback for later optimization
        return callback($cb)->invokeArgs($args);
      }
    }

    // Try it with autoloading
    if($allowAutoLoading) {
      if($this->autoLoadExtension()) return $this->__call2($method, $args, false);
    }

    throw new \LogicException('Call to undefined method: ' . $method);
  }

  /**
   * Autoload extensions
   * @return bool Something new was loaded
   */
  protected function autoLoadExtension() {
    if(!$md = Environment::getConfig('metadata') or !$md->extension) return false; //No metadata found in config
    $cnt = 0;

    foreach($md->extension as $extName => $extFactory) {
      if(!isset($this->extensions[$extName])) {
        $this->addExtension($extName, $this->createExtension($extFactory));
        $cnt++;
      }
    }

    return $cnt > 0;
  }

  public function getAllFieldNames() {
    return array_keys($this->reflFields);
  }

  public function getFieldNames() {
    return array_keys($this->fieldMappings);
  }

  public function getFieldDefinitions() {
    return $this->fieldMappings;
  }

  /**
   * Gets the ReflectionClass instance of the mapped class.
   * @deprecated Need to get rid of this
   * @return ReflectionClass
   */
  public function getReflectionClass() {
    if(!$this->reflClass) {
      $this->reflClass = new ReflectionClass($this->name);
    }
    return $this->reflClass;
  }

  /**
   * Gets the ReflectionProperty
   * @deprecated Need to get rid of this
   * @return ReflectionClass
   */
  public function _getNewReflectionProperty($class, $property) {
    return new ReflectionProperty($class, $property);
  }
}
