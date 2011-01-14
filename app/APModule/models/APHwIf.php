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
 * APHwIf
 *
 * @Table()
 * @Entity
 */
class APHwIf extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var Hw
   * @ManyToOne(targetEntity="APHw", inversedBy="HwIfs")
   * @JoinColumns({
   *   @JoinColumn(name="APHw", referencedColumnName="ID")
   * })
   */
  protected $Hw;

  /**
   * @var string $interface
   * @Column(name="interface", type="string", length=50, nullable=false)
   */
  protected $interface;

  /**
   * @var string $mac
   * @Column(name="mac", type="string", length=20, nullable=true)
   */
  protected $mac;

  /**
   * @var enum $typ
   * @Column(name="typ", type="enum", nullable=false)
   */
  protected $typ;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
  
  }
}