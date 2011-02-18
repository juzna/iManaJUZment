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

use \Doctrine\ORM\Events,
  Doctrine\ORM\Event,
  ActiveEntity\Entity;

class LiveConnect {
  protected static $socket;
  protected static $transport;
  protected static $protocol;
  protected static $client;

  // We don't allow creating instances
  private function __construct() { }

  /**
   * Prepare for sending notifications
   * @return void
   */
  protected static function initialize() {
    if(isset(self::$client)) return;

    // Initialize everything
    self::$socket = new TSocket('localhost', 9090);
    self::$transport = new TFramedTransport(self::$socket);
    self::$protocol = new TBinaryProtocol(self::$transport);
    self::$client = new /*\Thrift\LiveConnect\*/LiveConnectClient(self::$protocol);

    self::$transport->open();
  }

  /**
   * Send notification
   * @param  $userId
   * @param  $table
   * @param  $op
   * @param  $oldData
   * @param  $newData
   * @return bool
   */
  public static function notify($userId, $table, $op, $oldData, $newData) {
    // Try to initialize if not yet
    if(empty(self::$client)) {
      try {
        self::initialize();
      }
      catch(\Exception $e) {
        return false;
      }
    }

    // Send notification
    try {
      self::$client->notify($userId, $table, $op, $oldData, $newData);
    }
    catch(\Exception $e) {
      return false;
    }

    return true;
  }



  /**
   * Register Doctrine handler
   * @return void
   */
  public static function register($em) {
    /** @var $evm \Doctrine\Common\EventManager */
    $evm = $em->getEventManager();

    $evm->addEventListener(array(
      Events::postPersist,
      Events::postUpdate,
      Events::postRemove,
    ), new self);
  }

  /**
   * Handler when there is new entity persisted
   * @param Doctrine\ORM\Event\LifecycleEventArgs $args
   * @return void
   */
  public function postPersist(Event\LifecycleEventArgs $args) {
    $entity = $args->getEntity();
    $class = get_class($entity);
    $newData = $entity instanceof Entity ? $entity->toArray() : null;

    self::notify(null, $class, LiveConnect_LiveConnectOp::opAdd, null, $newData);
  }

  /**
   * Handler when existing entity is updated
   * @param Doctrine\ORM\Event\LifecycleEventArgs $args
   * @return void
   */
  public function postUpdate(Event\LifecycleEventArgs $args) {
    $entity = $args->getEntity();
    $class = get_class($entity);
    $newData = $entity instanceof Entity ? $entity->toArray() : null;

    self::notify(null, $class, LiveConnect_LiveConnectOp::opEdit, null, $newData);
  }

  /**
   * Handler when existing entity is removed
   * @param Doctrine\ORM\Event\LifecycleEventArgs $args
   * @return void
   */
  public function postRemove(Event\LifecycleEventArgs $args) {
    $entity = $args->getEntity();
    $class = get_class($entity);
    $newData = $entity instanceof Entity ? $entity->toArray() : null;

    self::notify(null, $class, LiveConnect_LiveConnectOp::opRemove, null, $newData);
  }
}
