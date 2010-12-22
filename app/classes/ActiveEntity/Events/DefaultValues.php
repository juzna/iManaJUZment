<?php

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
