<?php

namespace Tables;
use Doctrine;


class DoctrineEntityTableDefinition extends \Nette\Object implements ITableDefinition {
  private $entityName;
  private $entity;
  
  public function __construct($name) {
    $this->entityName = $name;
    $this->entity = Doctrine::getTable($name);
  }

  /**
   * Get unique ID of this table
   * @return string
   */
  public function getId() {
    return $this->getName();
  }
  
  /**
   * Get name which will be useable in JS
   * @return string
   */
  public function getName() {
    return "doctrine-$this->entityName";
  }

  /**
   * Get title of table
   * @return string
   */
  public function getTitle() {
    return $this->entityName;
  }
  
  /**
   * Get list of parameters, which the tables requests
   * @return array of TableParameter
   */
  public function getParameters() {
    return array();
  }
  
  /**
   * Get data source description
   * @return array Associative array of parameters, type tells which data source should be used
   */
  public function getDataSource() {
    return array(
      'type'  => 'd:table',
      'value' => $this->entityName,
    );
  }
  
  /**
   * Get list of fields
   * @return array of TableField
   */
  public function getFields() {
    $ret = array();
    
    foreach($this->entity->getFieldNames() as $i => $colName) {
      $ret[$colName] = new TableField($colName, array(
        'title'     => $colName,
        'variable'  => $colName,
        'show'      => ($i != 0 && $i < 8) ? 1 : 0,
      ));
    }
    
    return $ret;
  }
  
  /**
   * Get variable which is primary key
   * @return string
   */
  public function getFieldIndex() {
    $keys = $this->entity->getFieldNames();
    return $keys[0];
  }
  
  /**
   * Get default sort column
   */
  public function getSortField() {
    return $this->getFieldIndex();
  }
  
  /**
   * Get HTML code of container template
   */
  public function getContainer() {
    return null;
  }
  
  /**
   * Get last change of template definition (for cache invalidation)
   * @return int timesamp
   */
  public function getMTime() {
    return 0;
  }
}
