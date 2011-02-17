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
 * APSwIf
 *
 * @Table @Entity @ae:Behaviour
 * @ae:links(module="AP", presenter="dashboard", alias="swif", common={ "add", "edit", "clone", "delete" })
 */
class APSwIf extends \ActiveEntity\BehavioralEntity
{
  public static $_behaviours = array(
    'ActiveEntity\\Behaviours\\InetSpeed',
  );

  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="SwInterfaces")
   * @JoinColumns({
   *   @JoinColumn(name="AP", referencedColumnName="ID")
   * })
   * @ae:immutable @ae:required @ae:show @ae:title("AP#")
   */
  protected $AP;

  /**
   * @var string $interface
   * @Column(name="interface", type="string", length=50, nullable=false)
   * @ae:show @ae:name
   */
  protected $interface;

  /**
   * @var string $masterInterface
   * @Column(name="masterInterface", type="string", length=50, nullable=true)
   */
  protected $masterInterface;

  /**
   * @var enum $type
   * @Column(name="type", type="enum", nullable=true)
   * @ae:show
   */
  protected $type;

  /**
   * @var boolean $isNet
   * @Column(name="isNet", type="boolean", nullable=false)
   */
  protected $isNet;

  /**
   * @var string $bssid
   * @Column(name="bssid", type="string", length=20, nullable=true)
   */
  protected $bssid;

  /**
   * @var string $essid
   * @Column(name="essid", type="string", length=30, nullable=true)
   * @ae:show
   */
  protected $essid;

  /**
   * @var integer $frequency
   * @Column(name="frequency", type="integer", length=4, nullable=true)
   */
  protected $frequency;

  /**
   * @var boolean $enabled
   * @Column(name="enabled", type="boolean", nullable=false)
   */
  protected $enabled;

  /**
   * @var enum $encType
   * @Column(name="encType", type="enum", nullable=true)
   */
  protected $encType;

  /**
   * @var string $encKey
   * @Column(name="encKey", type="string", length=50, nullable=true)
   */
  protected $encKey;

  /**
   * @var integer $tarifFlag
   * @Column(name="tarifFlag", type="integer", length=11, nullable=false)
   */
  //protected $tarifFlag;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
  
  }
}
