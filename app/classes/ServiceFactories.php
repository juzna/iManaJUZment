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

use Doctrine\ORM\Configuration,
  Doctrine\ORM\EntityManager,
  Nette\Environment;


class ServiceFactories {
  public static $config = null;
  public static $em = null;

  /**
   * Get Doctrine's configuration object
   * @return null
   */
  public static function getDoctrineConfiguration() {
    return self::$config ?: self::createDoctrineConfiguration();
  }

  public static function createDoctrineConfiguration() {
    $config = new Configuration;

    // Metadata driver - annotations
    {
      $modelDirs = glob(APP_DIR . "/*Module/models/") + array(APP_DIR . '/models/');

      $config->setClassMetadataFactoryName('Juz\\ClassMetadataFactory');

      $reader = new \Doctrine\Common\Annotations\AnnotationReader();
      $reader->setDefaultAnnotationNamespace('Doctrine\\ORM\\Mapping\\');
      $reader->setAnnotationNamespaceAlias('ActiveEntity\\Annotations\\', 'ae');
      $reader->setAnnotationNamespaceAlias('Juz\\Forms\\Annotations\\', 'frm');
      $reader->setAutoloadAnnotations(true);

      $metadata = new \ActiveEntity\AnnotationDriver($reader, (array) $modelDirs);

      $config->setMetadataDriverImpl($metadata);
    }

    // Proxy
    $config->setProxyNamespace('Proxy');
    $config->setProxyDir(TMP_DIR . '/proxy');

    // Add debug panel
    $config->setSQLLogger(\Nella\Doctrine\Panel::createAndRegister());

    return self::$config = $config;
  }


  public static function getEntityManager() {
    return self::$em ?: self::createEntityManager();
  }

  /**
   * Creates Doctrine Entity manager
   * @return Doctrine\ORM\EntityManager
   */
  public static function createEntityManager() {
    // Get database config
    $database = (array) Environment::getConfig('database');
    $em = EntityManager::create($database, self::getDoctrineConfiguration());

    // Register event handlers
    $em->getEventManager()->addEventSubscriber(new \ActiveEntity\Events\DefaultValues);
    //\LiveConnect::register();

    return self::$em = $em;
  }
}
