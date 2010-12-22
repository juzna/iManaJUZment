<?php

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
  }



}
