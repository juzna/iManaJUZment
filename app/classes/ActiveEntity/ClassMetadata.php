<?php

namespace ActiveEntity;
use ActiveEntity\Reflection\ReflectionClass,
  ActiveEntity\Reflection\ReflectionProperty;


/**
 * Extended metadata for ActiveEntity's
 */
class ClassMetadata extends \Doctrine\ORM\Mapping\ClassMetadata {
  public $title;
  public $titles;
  public $listable = false;
  public $editable = false;
  public $notFoundAction;
  public $notFoundParams;
  
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
   * @return ReflectionClass
   */
  public function getReflectionClass() {
    if(!$this->reflClass) {
      $this->reflClass = new ReflectionClass($this->name);
    }
    return $this->reflClass;
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

