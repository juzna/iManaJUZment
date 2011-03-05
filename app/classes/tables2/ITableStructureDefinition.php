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

namespace Juz\Tables;

interface ITableStructureDefinition {
  /**
   * Get unique ID of this table to be used e.g. in JavaScript
   * @return string
   */
  public function getId();

  /**
   * Get title of table for headings
   * @return string
   */
  public function getTitle();

  /**
   * Get list of fields
   * @return array<TableField> Map of fields
   */
  public function getFields();

  /**
   * Get variable which is primary key
   * @return string
   */
  public function getFieldIndex();

  /**
   * Get default sort column
   * @return array
   */
  public function getSortFields();

  /**
   * Get last change of template definition (for cache invalidation)
   * @return int timesamp
   */
  public function getMTime();

  /**
   * Get list of links for header
   * @return array [ { title, module, presenter, view?, action?, params[] } ]
   */
  public function getHeaderLinks();

  /**
   * Get list of links for each row
   * @see getHeaderLinks
   * @return array
   */
  public function getItemLinks();
}
