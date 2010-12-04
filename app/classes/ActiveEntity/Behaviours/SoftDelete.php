<?php

namespace ActiveEntity\Behaviours;
use Doctrine\ORM\Event,
  ActiveEntity\BahavioralEntity;


class SoftDelete extends BaseBehaviour {
  public static function setDefinition($className, $args) {
    self::hasColumn('isDeleted', 'boolean', null, array('nullable' => false));
    self::hasColumn('deleted', 'datetime');
    
    self::hasMethod('delete', array('ActiveEntity\Behaviours\SoftDelete', 'callDelete'));
    
    // Register event listener
    $em = \ActiveEntity\Entity::getEntityManager();
    new SoftDeleteEventListener($em);
  }
  
  public static function callDelete($className, $oThis) {
    $oThis->isDeleted = true;
    $oThis->deleted = new \DateTime('now');
  }
}


class SoftDeleteEventListener {
  public function __construct(\Doctrine\ORM\EntityManager $em) {
    $eventList = array('create', 'preRemove');
    $em->getEventManager()->addEventListener($eventList, $this);
//    echo "SoftDelete event listener registered\n";
  }
  
  /**
   * Check, if entity is SoftDeletable (i.e. has this behaviour)
   */
  private static function isEntitySoftDelete($obj) {
    if(!is_subclass_of($obj, 'ActiveEntity\\BehavioralEntity')) return false;
    
    $className = get_class($obj);
    return $className::_hasBehaviour('ActiveEntity\\Behaviours\\SoftDelete');
  }
  
  public function create($ev) {
    if(!self::isEntitySoftDelete($ev->getEntity())) return; // Sorry, not interested in foreign entities
    $ev->getEntity()->isDeleted = false;
  }
  
  public function preRemove(Event\LifeCycleEventArgs $ev) {
    if(!self::isEntitySoftDelete($ev->getEntity())) return; // Sorry, not interested in foreign entities
    throw new \Exception("SoftDelete entities are not able to be removed completely");
  }
}
