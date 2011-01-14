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
 * APCoverage
 *
 * @Table @Entity
 * @ae:links(module="AP", presenter="dashboard", alias="coverage", common={ "add", "edit", "clone", "delete" }, {
 *   @ae:link(title="detail", view="coverageDetail", params={"$ID"})
 * })
 */
class APCoverage extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="Coverages")
   * @JoinColumns({
   *   @JoinColumn(name="AP", referencedColumnName="ID")
   * })
   * @ae:immutable @ae:required @ae:show @ae:title("AP#")
   */
  protected $AP;
  
  /**
   * @var string $interface
   * @Column(name="interface", type="string", length=50, nullable=true)
   * @ae:show
   */
  protected $interface;

  /**
   * @var integer $vlan
   * @Column(name="vlan", type="integer", length=4, nullable=true)
   */
  protected $vlan;

  /**
   * @var integer $adresa
   * @Column(name="adresa", type="integer", length=11, nullable=false)
   */
  protected $adresa;

  /**
   * @var string $poznamka
   * @Column(name="poznamka", type="string", length=255, nullable=true)
   */
  protected $poznamka;

  /**
   * @var integer $doporuceni
   * @Column(name="doporuceni", type="integer", length=1, nullable=false)
   */
  protected $doporuceni;

  /**
   * @var APCoverageSubnet
   * @OneToMany(targetEntity="APCoverageSubnet", mappedBy="Coverage")
   */
  protected $Subnets;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
    $this->Subnets = new ArrayCollection;
  }
}