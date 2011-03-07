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

namespace Juz\Tables\Creator;
use Juz\Tables\ITableStructureDefinition,
  Juz\Tables\IDataSource;

/**
 * Base class for table creators and renderers
 *
 */
abstract class BaseCreator extends \Nette\Object implements \Juz\Tables\ITableCreator {
  /** @var \Juz\Tables\ITableStructureDefinition */
  protected $definition = null;

  /** @var \Juz\Tables\IDataSource */
  protected $datasource = null;

  /**
   * Adds table definition to this creator
   * @param ITableStructureDefinition $def
   * @return void
   */
  function setTableDefinition(ITableStructureDefinition $def) {
    if(isset($this->definition)) throw new \LogicException('Table definition alredy set');
    $this->definition = $def;
  }

  /**
   * Adds datasource to this creator
   * @param IDataSource $ds
   * @return void
   */
  function setDataSource(IDataSource $ds) {
    if(isset($this->datasource)) throw new \LogicException('Table datasource alredy set');
    $this->datasource = $ds;
  }

  public function getDefinition() {
    return $this->definition;
  }

  public function getDatasource() {
    return $this->datasource;
  }

  /**
   * Get field value from misc formats of table row given
   * (it's public so that it can be called from closures)
   *
   * @param mixed $row See documentation for IDataSource
   * @param string $fieldName
   * @return mixed
   */
  public function getFieldValue($row, $fieldName) {
    if(is_array($row) || $row instanceof \ArrayAccess) return $row[$fieldName];
    else return $row->$fieldName;
  }
}
