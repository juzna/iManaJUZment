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
 * Inactivity of a customer
 *
 * @package Model\Customer
 * @Table @Entity
 */
class CustomerInactivity extends \ActiveEntity\Entity {
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var Customer $customer
   * @ManyToOne(targetEntity="Customer", inversedBy="Inactivities")
   * @JoinColumn(name="custId", referencedColumnName="custId")
   */
  protected $customer;

  /**
   * @var date $datumOd
   * @Column(name="datumOd", type="date", nullable=false)
   */
  protected $datumOd;

  /**
   * @var date $datumDo
   * @Column(name="datumDo", type="date", nullable=true)
   */
  protected $datumDo;

  /**
   * @var string $reason
   * @Column(name="reason", type="string", length=255, nullable=false)
   */
  protected $reason;

  /**
   * @ManyToMany(targetEntity="CustomerTariff", inversedBy="inactivities")
   * @JoinTable(name="CustomerInactivityTariff",
   *   joinColumns={ @JoinColumn(name="inactivityId", referencedColumnName="ID") },
   *   inverseJoinColumns={ @JoinColumn(name="customerTariffId", referencedColumnName="ID") }
   * )
   */
  protected $tariffs;

  public function __construct() {
    $this->tariffs = new ArrayCollection;
  }
}