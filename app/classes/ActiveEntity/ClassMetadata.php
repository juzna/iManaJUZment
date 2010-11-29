<?php

namespace ActiveEntity;

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
  
  public function getFieldNames() {
    return array_keys($this->fieldMappings);
  }
  
  public function getFieldDefinitions() {
    return $this->fieldMappings;
  }
  
}

