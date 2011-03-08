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
 * APIP
 *
 * @package Model\AP
 * @Table @Entity
 * @ae:links(module="AP", presenter="dashboard", alias="ip", common={ "add", "edit", "clone", "delete" })
 */
class APIP extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="IPs")
   * @JoinColumns({
   *   @JoinColumn(name="AP", referencedColumnName="ID")
   * })
   * @ae:immutable @ae:required @ae:show @ae:title("AP#")
   */
  protected $AP;
  
  /**
   * @var string $interface
   * @Column(name="interface", type="string", length=50, nullable=false)
   * @ae:show
   * @frm:Override @frm:EntitySelect(targetEntity="APSwIf", fieldName="interface", fieldId="interface", dependencies={"AP"})
   */
  protected $interface;

  /**
   * @var string $ip
   * @Column(name="ip", type="string", length=15, nullable=false)
   * @ae:title("IP address") @ae:show
   */
  protected $ip;

  /**
   * @var integer $netmask
   * @Column(name="netmask", type="integer", length=2, nullable=false)
   * @ae:show
   */
  protected $netmask;

  /**
   * @var string $description
   * @Column(name="description", type="string", length=255, nullable=true)
   */
  protected $description;

  /**
   * @var bool $enabled
   * @Column(name="enabled", type="boolean", nullable=false)
   */
  protected $enabled;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
  
  }
}