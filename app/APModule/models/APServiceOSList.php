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



use Doctrine\Common\Collections\ArrayCollection;

/**
 * APServiceOSList
 *
 * @Table()
 * @Entity
 */
class APServiceOSList extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var string $os
   * @Column(name="os", type="string", length=20, nullable=false)
   */
  protected $os;

  /**
   * @var string $version
   * @Column(name="version", type="string", length=20, nullable=false)
   */
  protected $version;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
  
  }
}