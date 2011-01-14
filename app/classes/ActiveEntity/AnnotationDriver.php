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


class AnnotationDriver extends \Doctrine\ORM\Mapping\Driver\AnnotationDriver {

  public function __construct(\Doctrine\Common\Annotations\AnnotationReader $reader, $paths = null) {
    parent::__construct($reader, $paths);
  }

  public function loadMetadataForClass($className, \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata) {
    // Load basic annotations
    parent::loadMetadataForClass($className, $metadata);
    
    $class = $metadata->getReflectionClass();
    $classAnnotations = $this->_reader->getClassAnnotations($class);
    
    // Continue only with metadata of ActiveEntity
    if(!($metadata instanceof ClassMetadata)) return;
    
    $prefix = 'ActiveEntity\Annotations\\';
    foreach($classAnnotations as $annotName => $annot) {
      if(strncmp($prefix, $annotName, strlen($prefix)) != 0) continue; // Not ours
      
      switch(substr($annotName, strlen($prefix))) {
        case 'Title':
          $metadata->title = $annot->value;
          $metadata->titles = (array) $annot;
          break;
          
        case 'Editable':
          $metadata->editable = true;
          break;
          
        case 'Listable':
          $metadata->listable = true;
          break;
          
        case 'Behaviour':
          $this->setUpBehaviour($className, $metadata, $annot);
          break;
          
        case 'NotFound':
          $metadata->notFoundAction = $annot->action;
          $metadata->notFoundParams = (array) $annot;
          break;
      }
    }
    
    // Populate fields annotations
    foreach ($class->getProperties() as $property) {
      if ($metadata->isMappedSuperclass && ! $property->isPrivate()
        ||
        $metadata->isInheritedField($property->name)
        ||
        $metadata->isInheritedAssociation($property->name)) {
        continue;
      }
      
      unset($field);
      $name = $property->getName();
      if(!isset($metadata->fieldMappings[$name])) continue;
      $field = &$metadata->fieldMappings[$name];
      
      foreach($this->_reader->getPropertyAnnotations($property) as $annotName => $annot) {
        if(strncmp($prefix, $annotName, strlen($prefix)) != 0) continue; // Not ours
        
        switch(substr($annotName, strlen($prefix))) {
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
}

