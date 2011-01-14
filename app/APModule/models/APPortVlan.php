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
 * APPortVlan
 *
 * @Table @Entity
 */
class APPortVlan extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="PortVlans")
   * @JoinColumns({
   *   @JoinColumn(name="AP", referencedColumnName="ID")
   * })
   */
  protected $AP;

  /**
   * @var string $port
   * @Column(name="port", type="string")
   */
  protected $port;

  /**
   * @var integer $vlan
   * @Column(name="vlan", type="integer")
   */
  protected $vlan;

  /**
   * @var boolean $tagged
   * @Column(name="tagged", type="boolean", nullable=false)
   */
  protected $tagged;

  /**
   * @var boolean $pvid
   * @Column(name="pvid", type="boolean", nullable=false)
   */
  protected $pvid;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
  
  }
}