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
 * Úhel se udává ve stupních při pohledu na mapu. Na východé je 0, na severu 90, západ 180, jih 270.<br />
 * Směr udává kam směruje anténa při pohledu na mapu. Rozsah je úhel, ve kterém vyzařuje signal. <br />
 * Dosah udává poloměr kruhové výseče v metrech, kde by měl být příjem bez problému.<br />
 */

use Doctrine\Common\Collections\ArrayCollection;

/**
 * APAntenna
 *
 * @Table @Entity
 * @ae:links(module="AP", presenter="dashboard", alias="antenna", common={ "add", "edit", "clone", "delete" })
 */
class APAntenna extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="Antennas")
   * @JoinColumns({
   *   @JoinColumn(name="AP", referencedColumnName="ID")
   * })
   * @ae:immutable @ae:required @ae:show @ae:title("AP#")
   */
  protected $AP;

  /**
   * @var string $interface
   * @Column(name="interface", type="string", length=50, nullable=false)
   */
  protected $interface;

  /**
   * @var integer $smer
   * @Column(name="smer", type="integer", length=3, nullable=false)
   */
  protected $smer;

  /**
   * @var integer $rozsah
   * @Column(name="rozsah", type="integer", length=3, nullable=false)
   */
  protected $rozsah;

  /**
   * @var integer $dosah
   * @Column(name="dosah", type="integer", length=4, nullable=false)
   */
  protected $dosah;

  /**
   * @var enum $polarita
   * @Column(name="polarita", type="enum", nullable=false)
   */
  protected $polarita;

  /**
   * @var integer $pasmo
   * @Column(name="pasmo", type="integer", length=2, nullable=true)
   */
  protected $pasmo;

  /**
   * @var string $poznamka
   * @Column(name="poznamka", type="string", length=255, nullable=true)
   */
  protected $poznamka;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
  
  }
}