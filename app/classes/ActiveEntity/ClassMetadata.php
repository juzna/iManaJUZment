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
use ActiveEntity\Reflection\ReflectionClass,
  ActiveEntity\Reflection\ReflectionProperty;



/**
 * Extended metadata for ActiveEntity's
 */
class Metadata implements \Juz\IExtensionSubscriber {
  /** @var \Juz\ClassMetaData Link to parent metadata */
  protected $metadata;

  public $title;
  public $titles;
  public $listable = false;
  public $editable = false;
  public $notFoundAction;
  public $notFoundParams;


  public function __construct(\Juz\ClassMetaData $metadata) {
    $this->metadata = $metadata;
  }

  /**
   * Gets a callback for named method
   * @param string $method
   * @return callback
   */
  public function loader($method) {
    // echo "Someone is lookign for $method\n";
    if(method_exists($this, $method)) return callback($this, $method);
  }


  /**
   * Get's title to be displayed
   *  - for creating table
   *  - for edit form
   */
  public function getTitle($what = null) {
    if(isset($this->titles[$what])) return $this->titles[$what];
    elseif(!empty($this->title)) return $this->title;
    else return $this->name;
  }

  /**
   * Get field, which represents name of this entity
   * @throws Exception
   * @return string
   */
  public function getNameField() {
    // Look for name field
    foreach($this->getFieldDefinitions() as $def) {
      if(!empty($def['fieldMetadata']['ActiveEntity\\Annotations\\Name'])) return $def['fieldName'];
    }

    // Try field with name 'name'
    if($this->metadata->hasField('name')) return 'name';

    throw new \Exception("Name field not found in entity");
  }

  public function getAllFieldNames() {
    return array_keys($this->metadata->reflFields);
  }
  
  public function getFieldNames() {
    return array_keys($this->metadata->fieldMappings);
  }
  
  public function getFieldDefinitions() {
    return $this->metadata->fieldMappings;
  }

  /**
   * Gets the ReflectionClass instance of the mapped class.
   * @return ReflectionClass
   */
  public function getReflectionClass() {
    if(!$this->metadata->reflClass) {
      $this->metadata->reflClass = new ReflectionClass($this->metadata->name);
    }
    return $this->metadata->reflClass;
  }

  public function _getNewReflectionProperty($class, $property) {
    return new ReflectionProperty($class, $property);
  }
  
  
/*  protected function _validateAndCompleteFieldMapping(array &$mapping) {
    \Doctrine\ORM\Mapping\ClassMetadataInfo::_validateAndCompleteFieldMapping($mapping);
    // Skip saving reflection property
  }
  */
}
