<?php

namespace Tables;


interface ITableDefinition {
  /**
   * Get unique ID of this table
   * @return string
   */
  public function getId();
  
  /**
   * Get name which will be useable in JS
   * @return string
   */
  public function getName();

  /**
   * Get title of table
   * @return string
   */
  public function getTitle();
  
  /**
   * Get list of parameters, which the tables requests
   * @return array of TableParameter
   */
  public function getParameters();
  
  /**
   * Get data source description
   * @return array Associative array of parameters, type tells which data source should be used
   */
  public function getDataSource();
  
  /**
   * Get list of fields
   * @return array of TableField
   */
  public function getFields();
  
  /**
   * Get variable which is primary key
   * @return string
   */
  public function getFieldIndex();
  
  /**
   * Get default sort column
   */
  public function getSortField();
  
  /**
   * Get HTML code of container template
   */
  public function getContainer();
  
  /**
   * Get last change of template definition (for cache invalidation)
   * @return int timesamp
   */
  public function getMTime();
}
