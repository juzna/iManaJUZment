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

namespace Juz\Tables\Definition;

use Juz\Tables\ITableStructureDefinition,
  Juz\Tables\Field,
  Juz\Tables\Parameter;


class DummyDefinition extends \Nette\Object implements ITableStructureDefinition {
  /**
   * Get unique ID of this table to be used e.g. in JavaScript
   * @return string
   */
  public function getId() {
    return 'dummy';
  }

  /**
   * Get title of table for headings
   * @return string
   */
  public function getTitle() {
    return 'Dummy table';
  }

  /**
   * Get list of fields
   * @return array<TableField> Map of fields
   */
  public function getFields() {
    return array(
      // ID
      'ID' => new Field('ID', array(
        'title'    => '#',
        'variable' => 'ID',
      )),

      // Interface
      'interface' => new Field('interface', array(
        'title'    => 'Interface',
        'variable' => 'interface',
      )),

      // IP address
      'ip' => new Field('ip', array(
        'title'   => 'IP address',
        'content' => '{$ip}/{$netmask}',
      )),
    );
  }

  /**
   * Get variable which is primary key
   * @return string
   */
  public function getFieldIndex() {
    return 'interface';
  }

  /**
   * Get default sort column
   * @return array
   */
  public function getSortFields() {
    return array(
      array('interface', 0)
    );
  }

  /**
   * Get last change of template definition (for cache invalidation)
   * @return int timesamp
   */
  public function getMTime() {
    return 0;
  }


  /**
   * Get list of links for header
   * @return array [ { title, module, presenter, view?, action?, params[] } ]
   */
  public function getHeaderLinks() {
    return null;
  }

  /**
   * Get list of links for each row
   * @see getHeaderLinks
   * @return array
   */
  public function getItemLinks() {
    return null;
  }
}
