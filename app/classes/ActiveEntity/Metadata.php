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
class Metadata extends \Nette\Object implements \Juz\IExtensionSubscriber {
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
    // echo "Someone is looking for $method\n";
    if(method_exists($this, $method)) return callback($this, $method);
  }
  
  /**
   * Receives annotations directed for this extension from AnnotationDriver
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
            $field['links'][] = $this->getLinkMetadataFromAnnotation($annot);
            break;

          case 'Links':
            if(!isset($field['links'])) $field['links'] = array();
            if(is_array($annot->value)) {
              foreach($annot->value as $link) $field['links'][] = $this->getLinkMetadataFromAnnotation($link);
            }
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

  protected function getLinkMetadataFromAnnotation(Annotations\Link $link) {
    return new LinkMetadata((array) $link);
  }

  /**
   * Gets ActiveEntity metadata for a specific field
   */
  public function getFieldMetadata($fieldName, $mdName = null) {
    if(!is_string($fieldName) || strlen($fieldName) < 1) throw new \InvalidArgumentException('$fieldName must be valid field name');
    if(!$this->classMetadata->hasField($fieldName) && !$this->classMetadata->hasAssociation($fieldName)) throw new \InvalidArgumentException('Field not exists');
    $ret = isset($this->classMetadata->fieldMappings[$fieldName]['ActiveEntity']) ? $this->classMetadata->fieldMappings[$fieldName]['ActiveEntity'] : null;

    if(isset($mdName)) return isset($ret[$mdName]) ? $ret[$mdName] : null;
    else return $ret;
  }

  /**
   * Get annotations of a field
   */
  public function getFieldAnnotations($fieldName, $annotationName = null) {
    if(!is_string($fieldName) || strlen($fieldName) < 1) throw new \InvalidArgumentException('$fieldName must be valid field name');
    if(!$this->classMetadata->hasField($fieldName) && !$this->classMetadata->hasAssociation($fieldName)) throw new \InvalidArgumentException('Field not exists');
    $ret = isset($this->fieldAnnotations[$fieldName]) ? $this->fieldAnnotations[$fieldName] : null;

    if(isset($annotationName)) return isset($ret[$annotationName]) ? $ret[$annotationName] : null;
    else return $ret;
  }


  /******** output methods ********/

  /**
   * Gets title to be displayed
   *  - for creating table
   *  - for edit form
   */
  public function getTitle($what = null) {
    if(isset($this->titles[$what])) return $this->titles[$what];
    elseif(!empty($this->title)) return $this->title;
    else return $this->name;
  }

  /**
   * Get field, which represents primary key for this entity
   * @throws Exception
   * @return string
   */
  public function getNameField() {
    foreach($this->classMetadata->getFieldNames() as $fieldName) {
      if($md = $this->getFieldMetadata($fieldName, 'Name')) return $fieldName;
    }

    // Try field with name 'name'
    if($this->classMetadata->hasField('name')) return 'name';

    throw new \Exception("Name field not found in entity");
  }

  /**
   * Get list of fields for default sorting
   * @return array of touples
   */
  public function getSortFields() {
    return array(
      array($this->getNameField(), 1),
    );
  }

  public function getRequiredFields() {
    $ret = array();
    foreach($this->classMetadata->getAssociationMappings() as $fieldName => $def) {
      if($this->getFieldAnnotations($fieldName, 'Required')) $ret[] = $fieldName;
    }

    return $ret;
  }

  /**
   * Get set of links to be displayed for each item
   * @return array<LinkMetadata>
   */
  public function getItemLinks() {
    static $ret = null;
    if($ret !== null) return $ret;

    $ret = array();

    // Some links are defined
    if(isset($this->classAnnotations['Links'])) {
      /** @var $links \ActiveEntity\Annotations\Links */
      $links = $this->classAnnotations['Links'];

      // Prepare common links
      if(is_array($links->common)) foreach($links->common as $item) {
        $linkDefinition = null;
        switch($item) {
          case 'detail':
          case 'edit':
          case 'clone':
          case 'remove':
          case 'delete':
            $indexField = $this->classMetadata->getSingleIdentifierFieldName();
            $linkDefinition = array(
              'title' => ucfirst($item),
              'view' => $item,
              'class' => 'in_dialog',
              'params' => array(
                $links->alias,
                '$' . $indexField,
              )
            );
            break;

            break;

          case 'add':
            // nothing
            break;
        }

        // Add new link
        if($linkDefinition) {
          foreach(array('module', 'presenter', 'view', 'action', 'params') as $f) if(!isset($linkDefinition[$f]) && isset($links->$f)) $linkDefinition[$f] = $links->$f;
          $ret[] = new LinkMetadata($linkDefinition);
        }
      }

      // Prepare explicit links
      if(is_array($links->value)) foreach($links->value as $link) {
        if($link instanceof Annotations\Link) {
          $link = (array) $link;

          // Get default values
          foreach(array('module', 'presenter', 'view', 'action', 'params') as $f) if(!isset($link[$f]) && isset($links->$f)) $link[$f] = $links->$f;
          $ret[] = new LinkMetadata($link);
        }
      }
    }

    return $ret;
  }

  /**
   * Get set of links to be displayed in table header
   * @return array<LinkMetadata>
   */
  public function getHeaderLinks() {
    static $ret = null;
    if($ret !== null) return $ret;

    $ret = array();

    // Some links are defined
    if(isset($this->classAnnotations['Links'])) {
      /** @var $links \ActiveEntity\Annotations\Links */
      $links = $this->classAnnotations['Links'];

      // Prepare common links
      if(is_array($links->common)) foreach($links->common as $item) {
        $linkDefinition = null;
        switch($item) {
          case 'detail':
          case 'edit':
          case 'clone':
          case 'remove':
          case 'delete':
            // Not for this section
            break;

          case 'add':
            $linkDefinition = array(
              'title' => ucfirst($item),
              'view' => $item,
              'class' => 'in_dialog',
              'params' => array(
                $links->alias,
              )
            );
            break;
        }

        // Add new link
        if($linkDefinition) {
          foreach(array('module', 'presenter', 'view', 'action', 'params') as $f) if(!isset($linkDefinition[$f]) && isset($links->$f)) $linkDefinition[$f] = $links->$f;
          $ret[] = new LinkMetadata($linkDefinition);
        }
      }

      // Prepare explicit links
      if(is_array($links->value)) foreach($links->value as $link) {
        if($link instanceof Annotations\HeaderLink) {
          $link = (array) $link;

          // Get default values
          foreach(array('module', 'presenter', 'view', 'action', 'params') as $f) if(!isset($link[$f]) && isset($links->$f)) $link[$f] = $links->$f;
          $ret[] = new LinkMetadata($link);
        }
      }
    }

    return $ret;
  }
}
