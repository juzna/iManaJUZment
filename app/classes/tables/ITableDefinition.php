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

  /**
   * Get list of links for each row
   * @return array [ { title, module, presenter, view?, action?, params[] } ]
   */
  public function getLinks();

  public function getHeaderLinks();
}
