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

/**
* Connect to database using Doctrine
*/


use Doctrine\ORM\Configuration,
  Doctrine\ORM\EntityManager,
  Nette\Environment;

$config = new Configuration;

// Metadata driver - annotations
{
  $modelDirs = glob(APP_DIR . "/*Module/models/") + array(APP_DIR . '/models/');

  $config->setClassMetadataFactoryName('ActiveEntity\ClassMetadataFactory');

  $reader = new \Doctrine\Common\Annotations\AnnotationReader();
  $reader->setDefaultAnnotationNamespace('Doctrine\ORM\Mapping\\');
  $reader->setAnnotationNamespaceAlias('ActiveEntity\Annotations\\', 'ae');
  $reader->setAutoloadAnnotations(true);
  
  $metadata = new \ActiveEntity\AnnotationDriver($reader, (array) $modelDirs);
  
  $config->setMetadataDriverImpl($metadata);
}

// Proxy
$config->setProxyNamespace('Proxy');
$config->setProxyDir(__DIR__ . '/../temp/proxy');

// Database
$database = (array) Environment::getConfig('database');
$em = EntityManager::create($database, $config);
ActiveEntity\Entity::setEntityManager($em);

$em->getEventManager()->addEventSubscriber(new \ActiveEntity\Events\DefaultValues);


// Add entity manager to context
Environment::getApplication()->getContext()->addService('Doctrine\\ORM\\EntityManager', $em);
$config->setSQLLogger(Nella\Doctrine\Panel::createAndRegister());

/**
 * @return Doctrine\ORM\EntityManager
 */
function em() { return $GLOBALS['em']; }

/**
 * @return Doctrine\ORM\Query
 */
function q() {
  $args = func_get_args();
  return call_user_func_array(array(em(), 'createQuery'), $args);
}

