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


namespace ActiveEntity\Behaviours;
use Doctrine\ORM\Event,
  ActiveEntity\BahavioralEntity;


class Timestampable extends BaseBehaviour {
  public static function setDefinition($className, $args) {
    self::hasColumn('created', 'datetime');
    self::hasColumn('updated', 'datetime');
    
    // Register event listener
    $em = \ActiveEntity\Entity::getEntityManager();
    new TimestampableEventListener($em);
  }  
}


class TimestampableEventListener {
  public function __construct(\Doctrine\ORM\EntityManager $em) {
    $eventList = array('prePersist', 'preUpdate');
    $em->getEventManager()->addEventListener($eventList, $this);
//    echo "Timestampable event listener registered\n";
  }
  
  /**
   * Check, if entity is Timestampable (i.e. has this behaviour)
   */
  private static function isEntityTimestampable($obj) {
    if(!is_subclass_of($obj, 'ActiveEntity\\BehavioralEntity')) return false;
    
    $className = get_class($obj);
    return $className::_hasBehaviour('ActiveEntity\\Behaviours\\Timestampable');
  }
  
  public function preUpdate(Event\PreUpdateEventArgs $ev) {
    if(!self::isEntityTimestampable($ev->getEntity())) return; // Sorry, not interested in foreign entities
    $x = & $ev->getEntityChangeSet();
    $x['updated'][0] = null;
    $x['updated'][1] = new \DateTime('now');
  }
  
  public function prePersist(Event\LifeCycleEventArgs $ev) {
    if(!self::isEntityTimestampable($ev->getEntity())) return; // Sorry, not interested in foreign entities
    $ev->getEntity()->setCreated(new \DateTime('now'))->setUpdated(new \DateTime('now'));
  }


}
