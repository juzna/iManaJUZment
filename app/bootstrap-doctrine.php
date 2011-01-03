<?php
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

