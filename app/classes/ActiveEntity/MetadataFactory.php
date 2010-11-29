<?php

namespace ActiveEntity;

/**
 * Factory for customized ClassMetadata info
 */
class ClassMetadataFactory extends \Doctrine\ORM\Mapping\ClassMetadataFactory {
  protected function newClassMetadataInstance($className) {
    return new ClassMetadata($className);
  }
}

