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

  public $classAnnotations;
  public $fieldAnnotations;

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

  /**
   * Gets a callback for named method; used for automagical method calls on ClassMetaData
   * @param string $method
   * @return callback
   */
  public function loader($method) {
    // echo "Someone is lookign for $method\n";
    if(method_exists($this, $method)) return callback($this, $method);
  }
  
  /**
   * Receives annotations for this extension
   * @param array $classAnnotations
   * @param array $fieldsAnnotations
   * @return void
   */
  public function setAnnotations($classAnnotations, $fieldsAnnotations) {
    $this->classAnnotations = $classAnnotations;
    $this->fieldAnnotations = $fieldsAnnotations;

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
      $field = &$this->classMetadata->fieldMappings[$fieldName]['ActiveEntity'];

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

          case 'Link':
            $field['links'][] = $annot;
            break;

          case 'Links':
            if(!isset($field['links'])) $field['links'] = array();
            if(is_array($annot->value)) $field['links'] += $annot->value;
            break;
        }
      }
    }
  }

  /**
   * Gets ActiveEntity metadata for a specific field
   */
  public function getFieldMetadata($fieldName, $mdName = null) {
    if(!isset($this->classMetadata->fieldMappings[$fieldName])) throw new \InvalidArgumentException('Field not exists');
    $ret = isset($this->classMetadata->fieldMappings[$fieldName]['ActiveEntity']) ? $this->classMetadata->fieldMappings[$fieldName]['ActiveEntity'] : null;

    if(isset($mdName)) return isset($ret[$mdName]) ? $ret[$mdName] : null;
    else return $ret;
  }

  /**
   * Set up new behaviour for entity
   */
  private function setUpBehaviour($className, \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata, Annotations\Behaviour $annot) {
    $className::_setupBehavioralMetadata($metadata);
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
    foreach($this->getFieldNames() as $field) {
      $md = $this->getFieldMetadata($field);
      if(isset($md['Name'])) return $field;
    }

    // Try field with name 'name'
    if($this->classMetadata->hasField('name')) return 'name';

    throw new \Exception("Name field not found in entity");
  }
}
