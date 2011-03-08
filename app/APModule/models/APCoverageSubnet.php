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
 * APPokrytiSubnet
 *
 * @package Model\AP
 * @Table @Entity
 * @ae:links(module="AP", presenter="dashboard", alias="coverageSubnet", common={ "add", "edit", "clone", "delete" })
 */
class APCoverageSubnet extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var APCoverage
   * @ManyToOne(targetEntity="APCoverage", inversedBy="Subnets")
   * @JoinColumns({
   *   @JoinColumn(name="coverage", referencedColumnName="ID")
   * })
   * @ae:immutable @ae:required
   */
  protected $Coverage;

  /**
   * @var string $ip
   * @Column(name="ip", type="string", length=15, nullable=false)
   * @ae:title("Subnet address")
   */
  protected $ip;

  /**
   * @var integer $netmask
   * @Column(name="netmask", type="integer", length=2, nullable=false)
   */
  protected $netmask;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
  }
}