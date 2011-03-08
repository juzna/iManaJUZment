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
 * APService
 *
 * @package Model\AP
 * @Table @Entity
 */
class APService extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="Services")
   * @JoinColumns({
   *   @JoinColumn(name="AP", referencedColumnName="ID")
   * })
   */
  protected $AP;

  /**
   * @var string $state
   * @Column(name="state", type="string", length=20, nullable=false)
   */
  protected $state;

  /**
   * @var string $stateText
   * @Column(name="stateText", type="string", length=100, nullable=true)
   */
  protected $stateText;

  /**
   * @var timestamp $lastCheck
   * @Column(name="lastCheck", type="timestamp", nullable=true)
   */
  protected $lastCheck;

  /**
   * @var APServiceList
   * @ManyToOne(targetEntity="APServiceDefinition")
   * @JoinColumns({
   *   @JoinColumn(name="service", referencedColumnName="code")
   * })
   */
  protected $Definition;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
  
  }
}