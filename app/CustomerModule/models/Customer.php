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
 * Customer
 *
 * @Table @Entity
 */
class Customer extends \ActiveEntity\Entity {
  /**
   * @var integer
   * @Column(name="custId", type="integer") @Id @GeneratedValue
   */
  protected $custId;

  /**
   * @var integer $contractNumber
   * @Column(name="contractNumber", type="string", length=20, nullable=false, unique=true)
   * @ae:name
   */
  protected $contractNumber;

  /**
   * @var string $password Password for intranet
   * @Column(name="password", type="string", length=50, nullable=true)
   */
  protected $password;

  /**
   * @var date $activeSince
   * @Column(name="activeSince", type="date", nullable=true)
   */
  protected $activeSince;

  /**
   * @var boolean $accepted
   * @Column(name="accepted", type="boolean", nullable=false)
   */
  protected $accepted;

  /**
   * @var integer $acceptedByUser Accepted by who?
   * @Column(name="accepteBydUser", type="integer", nullable=true)
   */
  protected $acceptedByUser;

  /**
   * @var timestamp $acceptedTime When was he accepted
   * @Column(name="acceptedTime", type="timestamp", nullable=true)
   */
  protected $acceptedTime;

  /**
   * @var date $prepaidDate
   * @Column(name="prepaidDate", type="date", nullable=true)
   */
  protected $prepaidDate;

  /**
   * @var boolean $active
   * @Column(name="active", type="boolean", nullable=false)
   */
  protected $active;

  /**
   * @var boolean $nepocitatPredplatne
   * @Column(name="nepocitatPredplatne", type="boolean", nullable=false)
   */
  protected $nepocitatPredplatne;

  /**
   * @var string $nepocitatPredplatneDuvod
   * @Column(name="nepocitatPredplatneDuvod", type="string", length=255, nullable=true)
   */
  protected $nepocitatPredplatneDuvod;

  /**
   * @var float $instalacniPoplatek
   * @Column(name="instalacniPoplatek", type="float", nullable=true)
   */
  protected $instalacniPoplatek;

  /**
   * @var integer $doporucitel
   * @Column(name="doporucitel", type="integer", length=11, nullable=true)
   */
  protected $doporucitel;

  /**
   * @var date $sepsaniSmlouvy
   * @Column(name="sepsaniSmlouvy", type="date", nullable=true)
   */
  protected $sepsaniSmlouvy;

  /**
   * @var integer $neplaticSkupina
   * @Column(name="neplaticSkupina", type="integer", nullable=true)
   */
  protected $neplaticSkupina;

  /**
   * @var integer $neplaticTolerance
   * @Column(name="neplaticTolerance", type="integer", nullable=true)
   */
  protected $neplaticTolerance;

  /**
   * @var date $neplaticNeresitDo
   * @Column(name="neplaticNeresitDo", type="date", nullable=true)
   */
  protected $neplaticNeresitDo;

  /**
   * @var array of CustomerAddress
   * @OneToMany(targetEntity="CustomerAddress", mappedBy="customer", cascade={"all"})
   */
  protected $Addresses;

  /**
   * @var array of CustomerContact
   * @OneToMany(targetEntity="CustomerContact", mappedBy="customer", cascade={"all"})
   */
  protected $Contacts;

  /**
   * @var array of CustomerIP
   * @OneToMany(targetEntity="CustomerIP", mappedBy="customer", cascade={"all"})
   */
  protected $IPs;

  /**
   * @var array of CustomerTariff
   * @OneToMany(targetEntity="CustomerTariff", mappedBy="customer", cascade={"all"})
   */
  protected $Tariffs;

  /**
   * @var array of CustomerInstalationFee
   * @OneToMany(targetEntity="CustomerInstalationFee", mappedBy="customer", cascade={"all"})
   */
  protected $InstalationFees;

  /**
   * @var array of CustomerServiceFee
   * @OneToMany(targetEntity="CustomerServiceFee", mappedBy="customer", cascade={"all"})
   */
  protected $ServiceFees;

  /**
   * @var array of CustomerInactivity
   * @OneToMany(targetEntity="CustomerInactivity", mappedBy="customer", cascade={"all"})
   */
  protected $Inactivities;

  /**
   * @var CustomerAddress Default address of a customer
   * @OneToOne(targetEntity="CustomerAddress")
   * @JoinColumn(name="defaultAddress", referencedColumnName="ID")
   */
  protected $address;


  public function __construct() {
    $this->Addresses = new ArrayCollection;
    $this->Contacts = new ArrayCollection;
    $this->IPs = new ArrayCollection;
    $this->Tariffs = new ArrayCollection;
    $this->Inactivities = new ArrayCollection;
    $this->InstalationFees = new ArrrayCollection;
    $this->ServiceFees = new ArrayCollection;
  }

  /**
   * Get all payments of this user
   * @return array
   */
  public function getPayments() {
    return \Payment::getRepository()->findBy(array('customer' => $this->custId));
  }

  /**
   * Get list of paymees which are available to be paid
   * @return array
   */
  public function getAvailablePaymees() {
    return array_merge(
      $this->getAvailablePaymeesTariff(),
      $this->getAvailablePaymeesInstalationFee(),
      $this->getAvailablePaymeesServiceFee()
    );
  }

  public function getAvailablePaymeesTariff() {
    $ret = array();
    foreach($this->Tariffs as $tariff) $ret += $tariff->getAvailablePaymees();
    return $ret;
  }

  public function getAvailablePaymeesInstalationFee() {
    $ret = array();

    foreach($this->InstalationFees as $item) {
      $x = $item->getAmountToBePaid();
      if($x > 0) $ret[] = array(
        'type'    => 'install-fee',
        'index'   => $item->ID,
        'amount'  => $x,
      );
    }

    return $ret;
  }

  public function getAvailablePaymeesServiceFee() {
    $ret = array();

    foreach($this->ServiceFees as $item) {
      $x = $item->getAmountToBePaid();
      if($x > 0) $ret[] = array(
        'type'    => 'service-fee',
        'index'   => $item->ID,
        'amount'  => $x,
      );
    }

    return $ret;
  }

  /**
   * Get full name as string
   * @return string
   */
  public function getFullName() {
    return $this->address ? $this->address->getFullName() : "[$this->contractNumber]";
  }


}
