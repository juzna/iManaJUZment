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


/**
 * Doctrine annotation driver which supports extensible metadata
 *
 * Loads config.ini and makes mappig to extensions based on configuration lines like this:
 *   annotations.mapping.NAMESPACE = EXTENSION-NAME
 *
 * Reads class metadata and also field metadata, which are sent to metadata extension using
 *   method setAnnotations($classMetadata, $fieldsMetadata)
 *
 * @author Jan Dolecek - juzna.cz
 *
 */
class AnnotationDriver extends \Doctrine\ORM\Mapping\Driver\AnnotationDriver {

  public function __construct(\Doctrine\Common\Annotations\AnnotationReader $reader, $paths = null) {
    parent::__construct($reader, $paths);
  }

  public function loadMetadataForClass($className, \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata) {
    // Load basic annotations
    parent::loadMetadataForClass($className, $metadata);

    // Assign metadata to extensions (if supported)
    if($metadata instanceof ClassMetadata) $this->assignMetadataToExtensions($metadata);
  }

  /**
   * Assign metadata to extensions
   * @return void
   */
  protected function assignMetadataToExtensions(ClassMetadata $metadata) {
    /** @var $property \ReflectionProperty */
    /** @var $class \ReflectionClass */

    $mapping = self::getExtensionMapping();

    // Output table
    $classMetadata = $fieldMetadata = array();

    // Prepare class metadata
    $class = $metadata->getReflectionClass();
    $classAnnotations = $this->_reader->getClassAnnotations($class);
    foreach($classAnnotations as $name => $value) {

      // Try to map this annotation to some extension
      foreach($mapping as $ns => $ext) {
        if(strncmp($name, $ns, strlen($ns)) === 0) $classMetadata[$ext][substr($name, strlen($ns))] = $value;
      }
    }

    // Prepare field metadata
    foreach($class->getProperties() as $property) {
      $propertyName = $property->getName();
      $propertyAnnotations = $this->_reader->getPropertyAnnotations($property);

      foreach($propertyAnnotations as $name => $value) {

        // Try to map this annotation to some extension
        foreach($mapping as $ns => $ext) {
          if(strncmp($name, $ns, strlen($ns)) === 0) $fieldMetadata[$ext][$propertyName][substr($name, strlen($ns))] = $value;
        }
      }
    }


    // Map output tables to extensions
    foreach($classMetadata as $ext => $annotations) {
      $metadata->getExtension($ext)->setAnnotations($annotations, @$fieldMetadata[$ext]);
    }
  }

  /**
   * Get mapping from annotation class namespace to extension name
   * @return array where keys are namespace prefixes and values are extension names
   */
  public static function getExtensionMapping() {
    static $mapping; // Use a cache ;)
    if(isset($mapping)) return $mapping;

    if(!$config = \Nette\Environment::getConfig('annotations') or !$config->mapping) return $mapping = false;

    $ret = array();
    foreach($config->mapping as $ns => $ext) {
      $ns = str_replace('-', '\\', $ns);
      $ret[$ns . '\\'] = $ext;
    }

    return $mapping = $ret;
  }
}
