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

interface IDataSourceDefinition {
  /**
   * Get type of data source to be used
   * @return string
   */
  function getType();

  /**
   * Get list of parameters which this given datasource requires
   * @return array<Parameter>
   */
  function getParameters();

  /**
   * Get options for this datasource
   * @return array
   */
  function getOptions();
}
