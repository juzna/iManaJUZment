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
 * APVlan
 *
 * @package Model\AP
 * @Table @Entity
 * @ae:links(module="AP", presenter="dashboard", alias="vlan", common={ "add", "edit", "clone", "delete" })
 */
class APVlan extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="Vlans")
   * @JoinColumns({
   *   @JoinColumn(name="AP", referencedColumnName="ID")
   * })
   * @ae:immutable @ae:required @ae:show @ae:title("AP#")
   */
  protected $AP;

  /**
   * @var integer $vlan
   * @Column(name="vlan", type="integer")
   * @ae:show @ae:title("VLAN #")
   */
  protected $vlan;

  /**
   * @var string $description
   * @Column(name="description", type="string", length=255, nullable=true)
   * @ae:show
   */
  protected $description;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
  
  }
}