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
  protected $classMetadata;
  protected $className;

  public $title;
  public $titles;
  public $listable = false;
  public $editable = false;
  public $notFoundAction;
  public $notFoundParams;


  public function __construct($className, \Juz\ClassMetaData $classMetadata) {
    $this->className = $className;
    $this->classMetadata = $classMetadata;
  }

  public function setAnnotations($classAnnotations, $fieldsAnnotations) {
    foreach($classAnnotations as $annotName => $annot) {
      switch($annotName) {
        case 'Title':
          $this->title = $annot->value;
          $this->titles = (array) $annot;
          break;

        case 'Editable':
          $this->editable = true;
          break;

        case 'Listable':
          $this->listable = true;
          break;

        case 'Behaviour':
          $this->setUpBehaviour($this->className, $this->classMetadata, $annot);
          break;

        case 'NotFound':
          $this->notFoundAction = $annot->action;
          $this->notFoundParams = (array) $annot;
          break;
      }
    }

    foreach($fieldsAnnotations as $fieldName => $fieldAnnotations) {
      if(!isset($this->classMetadata->fieldMappings[$fieldName])) continue;
      $field = &$this->classMetadata->fieldMappings[$fieldName];

      foreach($fieldAnnotations as $annotName => $annot) {
        switch($annotName) {
          case 'Title':
            $field['title'] = $annot->value;
            break;

          case 'Get':
            $field['autoGetter'] = true;
            break;

          case 'Set':
            $field['autoSetter'] = true;
            break;

          case 'Format':
            $field['format'] = $annot->value;
            break;

          case 'Show':
            $field['showByDefault'] = true;
            break;
        }
      }
    }
  }

  /**
   * Set up new behaviour for entity
   */
  private function setUpBehaviour($className, \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata, Annotations\Behaviour $annot) {
    $className::_setupBehavioralMetadata($metadata);
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
    if($this->classMetadata->hasField('name')) return 'name';

    throw new \Exception("Name field not found in entity");
  }

  public function getAllFieldNames() {
    return array_keys($this->classMetadata->reflFields);
  }
  
  public function getFieldNames() {
    return array_keys($this->classMetadata->fieldMappings);
  }
  
  public function getFieldDefinitions() {
    return $this->classMetadata->fieldMappings;
  }  
}
