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
 * APPort
 *
 * @package Model\AP
 * @Table @Entity
 * @ae:links(module="AP", presenter="dashboard", alias="port", common={ "add", "edit", "clone", "delete" })
 */
class APPort extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="Ports")
   * @JoinColumns({
   *   @JoinColumn(name="AP", referencedColumnName="ID")
   * })
   * @ae:immutable @ae:required @ae:show @ae:title("AP#")
   */
  protected $AP;

  /**
   * @var string $port
   * @Column(name="port", type="string", length=50, nullable=false)
   * @ae:show
   */
  protected $port;

  /**
   * @var enum $typ
   * @Column(name="typ", type="enum", nullable=false)
   * @ae:show
   */
  protected $typ;

  /**
   * @var integer $PorCis
   * @Column(name="PorCis", type="integer", length=11, nullable=true)
   */
  protected $PorCis;

  /**
   * @var integer $odbernaAdresa
   * @Column(name="odbernaAdresa", type="integer", length=11, nullable=true)
   */
  protected $odbernaAdresa;

  /**
   * @var string $cisloVchodu
   * @Column(name="cisloVchodu", type="string", length=20, nullable=true)
   */
  protected $cisloVchodu;

  /**
   * @var string $cisloBytu
   * @Column(name="cisloBytu", type="string", length=20, nullable=true)
   */
  protected $cisloBytu;

  /**
   * @var integer $connectedTo
   * @Column(name="connectedTo", type="integer", length=11, nullable=true)
   */
  protected $connectedTo;

  /**
   * @var string $connectedInterface
   * @Column(name="connectedInterface", type="string", length=50, nullable=true)
   */
  protected $connectedInterface;

  /**
   * @var string $connectedPort
   * @Column(name="connectedPort", type="string", length=50, nullable=true)
   */
  protected $connectedPort;

  /**
   * @var boolean $isUplink
   * @Column(name="isUplink", type="boolean", nullable=false)
   */
  protected $isUplink;

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