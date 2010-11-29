<?php

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
}
