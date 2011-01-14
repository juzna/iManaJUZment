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


namespace ActiveEntity\Events;

use ActiveEntity\Entity,
  ActiveEntity\Reflection\ReflectionProperty;

class DefaultValues implements \Doctrine\Common\EventSubscriber {

  public function prePersist(\Doctrine\ORM\Event\LifecycleEventArgs $event) {
    $entity = $event->getEntity();
    $className = get_class($entity);
    if(!($entity instanceof Entity)) return;

    // Set default values where not set
    $metadata = Entity::getClassMetadata($className);
    foreach($metadata->getFieldDefinitions() as $fieldName => $definition) {
      $prop = new ReflectionProperty($className, $fieldName);
      $prop->setAccessible(true);
      $val = $prop->getValue($entity);

      // Not set
      if($val === null && isset($definition['fieldMetadata']['ActiveEntity\\Annotations\\DefaultValue'])) {
        $prop->setValue($entity, $definition['fieldMetadata']['ActiveEntity\\Annotations\\DefaultValue']->value);
      }
    }
  }

  /**
   * Returns an array of events this subscriber wants to listen to.
   * @return array
   */
  public function getSubscribedEvents() {
    return array(
      \Doctrine\ORM\Events::prePersist,
    );
  }
}
