<?php

namespace ActiveEntity;
use Doctrine\ORM\Proxy\Proxy;


class Helper {
  /**
   * Helper loader (based on name it gives helper callback)
   */
  public static function loader($helper) {
    $callback = callback('ActiveEntity\\Helper', $helper);
    if ($callback->isCallable()) {
      return $callback;
    }
  }

  /**
   * Name field of an entity
   */
  public static function name($entity) {
    if(!$entity) return;
    
    $metadata = Entity::getClassMetadata($entity);
    $field = $metadata->getNameField();

    return $entity->$field;
  }

  /**
   * Bool value
   */
  public static function bool($val) {
    return $val ? 'Y' : 'N';
  }

  /**
   * Dumps all variables from entity into a table
   * TODO: use metadata for title
   */
  public static function dumpVariables(Entity $entity, $tableHeader = true) {
    /** @var $metadata ClassMetadata */
    $metadata = $entity::getClassMetaData();

    if($tableHeader) echo "<table>\n";

    foreach($metadata->getFieldNames() as $field) {
      echo "\t<tr><th>$field</th>\t<td>{$entity->$field}</td></tr>\n";
    }

    if($tableHeader) echo "</table>\n";
  }

  /**
   * Dump template code, which shows all variables
   * TODO: use metadata for title
   */
  public static function dumpDefinitionVariables(Entity $entity, $tableHeader = true) {
    /** @var $metadata ClassMetadata */
    $metadata = $entity::getClassMetaData();
    $fields = $metadata->getFieldNames();
    $len = max(array_map('strlen', $fields)) + 2; // Max length of field name

    if($tableHeader) echo "<table>\n";

    foreach($fields as $field) {
      $spaces = str_repeat(' ', $len - strlen($field));
      echo "\t<tr><th>$field:</th>$spaces<td>{\$$field}</td></tr>\n";
    }

    if($tableHeader) echo "</table>\n";
  }

  public static function dumpDefinitionTables(Entity $entity, $var) {
    $mappings = $entity->getClassMetadata()->getAssociationMappings();

    foreach($mappings as $item) {
      $fieldName = $item['fieldName'];
      $te = $item['targetEntity'];

      echo "{table $te, {$var}->$fieldName}\n";
    }
  }

  public static function DoctrineProxyIdentifier($item, $fieldName = 'ID') {
    if(!isset($item)) return;

    if($item instanceof Proxy) {
      $prop = new \ReflectionProperty($item, '_identifier');
      $prop->setAccessible(true);
      $ret = $prop->getValue($item);
      return $ret[$fieldName];
    }

    else {
      return $item->$fieldName;
    }
  }

}