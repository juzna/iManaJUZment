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
 * APServiceList
 *
 * @Table @Entity
 */
class APServiceDefinition extends \ActiveEntity\Entity
{
  /**
   * @var string $code
   * @Column(name="code", type="string") @Id @GeneratedValue
   */
  protected $code;

  /**
   * @var string $nazev
   * @Column(name="nazev", type="string", length=50, nullable=false)
   */
  protected $nazev;

  /**
   * @var string $popis
   * @Column(name="popis", type="string", length=255, nullable=true)
   */
  protected $popis;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
  
  }
}