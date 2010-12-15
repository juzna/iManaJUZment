<?php

namespace Tables;
use Doctrine;


class DoctrineEntityTableDefinition extends \Nette\Object implements ITableDefinition {
  private $entity;
  private $metadata;
  
  public function __construct($name) {
    $this->entity = $name;
    $this->metadata = \ActiveEntity\Entity::getClassMetadata($name);
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
    return "doctrine-$this->entity";
  }

  /**
   * Get title of table
   * @return string
   */
  public function getTitle() {
    return $this->metadata->getTitle();
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
      'value' => $this->entity,
    );
  }
  
  /**
   * Get list of fields
   * @return array of TableField
   */
  public function getFields() {
    $ret = array();
    $cntVisible = 0;

    \Nette\Debug::barDump($this->metadata->getFieldDefinitions(), 'Field def');

    foreach($this->metadata->getFieldDefinitions() as $colName => $definition) {
      $ret[$colName] = $field = new TableField($colName, array(
        'title'     => isset($definition['title']) ? $definition['title'] : ucfirst($colName),
        'variable'  => $colName,
        'show'      => $show = !empty($definition['showByDefault']),
      ));
      $this->_setupTableFieldFromMetadata($field, $definition);
      if($show) $cntVisible++;
    }
    
    // No cols have show parameter -> try to guess
    if(!$cntVisible) {
      foreach(array_keys($ret) as $i => $name) $ret[$name]->show = ($i != 0 && $i < 8) ? 1 : 0;
    }
    
    return $ret;
  }

  protected function _setupTableFieldFromMetadata(TableField $field, array $def) {
    if(!$md = @$def['fieldMetadata']) return;

    if($link = @$md['ActiveEntity\\Annotations\\Link']) {
      $field->parameters['link'] = $link;
    }
  }
  
  /**
   * Get variable which is primary key
   * @return string
   */
  public function getFieldIndex() {
    $keys = $this->metadata->getFieldNames();
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
