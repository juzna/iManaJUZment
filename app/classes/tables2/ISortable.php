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

/**
 * DataSource can implement this interface to tell it supports sorting
 */
interface ISortable {
  // Order constants
  const ORDER_UNSPECIFIED = 0,
    ORDER_ASCENDING = 1,
    ORDER_DESCENDING = 2;

  /**
   * Add field to sorting
   * @param string $field
   * @param int $order
   * @return \Juz\Tables\ISortable Provides a fluent interface
   */
  function sortBy($field, $order = self::ORDER_ASCENDING);

  /**
   * Get list of fields used to sort dataset
   * @return array of touples $fieldName:$order
   */
  function getSortFields();

  /**
   * Clear all defined sorting
   * @return \Juz\Tables\ISortable Provides a fluent interface
   */
  function clearSorting();
}
