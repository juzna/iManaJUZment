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




/**
 * APTag
 * @Table @Entity
 */
class APTag extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;
  
  /**
   * @var string $name
   * @Column(name="name", type="string", length=100, nullable=false, unique=true)
   */
  protected $name;

  /**
   * @Column(name="color", type="string", length=50, nullable=true)
   */
  protected $color;

  /**
   * Get all tags in database
   * @return array of strings
   */
  public static function getAllTagNames() {
    return dibi::query("select distinct [name] from [APTag] order by [name]")->fetchPairs(null, 'name');
  }
}
