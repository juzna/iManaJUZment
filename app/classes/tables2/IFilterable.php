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

interface IFilterable {
  /**
   * Get list of all defined filters
   * @return array
   */
  function getFilters();

  /**
   * Remove all filters
   * @return void
   */
  function clear();

  /**
   * Add new filter
   * @param string $field Field name to be matched
   * @param string $op Operation: ==, <, >, >=, <=, in, preg
   * @param mixed $value Reference value
   * @return void
   */
  function addFilter($field, $op, $value);
}
