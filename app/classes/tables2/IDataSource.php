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
 * Simple data source for tables
 *
 * Acts as an array (or traversable object), where each item is on object representing table row.
 *  This table row can be any object with:
 *   - public properties
 *   - magic method __get returning values for this properties
 *   - an array where keys are column names
 *   - object implementing ArrayAccess interface and returning column values by offsetGet() method
 *
 * Data source class can also implement IFilterable, IPagable and ISortable interfaces to support more features
 *  for table creators, which are able to use this features
 */
interface IDataSource extends \Countable, \Iterator, \ArrayAccess {
  // nothing
}
