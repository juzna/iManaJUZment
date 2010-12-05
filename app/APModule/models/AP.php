<?php


use Doctrine\Common\Collections\ArrayCollection;

/**
 * AP
 *
 * @Table()
 * @Entity @ae:Behaviour
 */
class AP extends \ActiveEntity\BehavioralEntity
{
  public static $_behaviours = array(
    'ActiveEntity\\Behaviours\\GeographicalCZ',
    'ActiveEntity\\Behaviours\\Timestampable',
    'ActiveEntity\\Behaviours\\SoftDelete',
    'ActiveEntity\\Behaviours\\Taggable' => array(
      'targetEntity'  => 'APTag',
      'targetEntityProperty'  => 'AP',
    ),
  );

  /**
   * @var integer $ID
   * @Column(name="ID", type="integer")
   * @Id
   * @GeneratedValue
   */
  protected $ID;

  /**
   * @var string $name
   * @Column(name="name", type="string", length=100, nullable=false, unique=true)
   */
  protected $name;

  /**
   * @var string $description
   * @Column(name="description", type="string", length=255, nullable=true)
   */
  protected $description;

  /**
   * @var enum $mode
   * @Column(name="mode", type="enum", nullable=true)
   */
  protected $mode;

  /**
   * @var string $IP
   * @Column(name="IP", type="string", length=15, nullable=true)
   */
  protected $IP;

  /**
   * @var integer $netmask
   * @Column(name="netmask", type="integer", length=2, nullable=true)
   */
  protected $netmask;

  /**
   * @var integer $pvid
   * @Column(name="pvid", type="integer", length=4, nullable=true)
   */
  protected $pvid;

  /**
   * @var boolean $snmpAllowed
   * @Column(name="snmpAllowed", type="boolean", nullable=true)
   */
  protected $snmpAllowed;

  /**
   * @var string $snmpCommunity
   * @Column(name="snmpCommunity", type="string", length=50, nullable=true)
   */
  protected $snmpCommunity;

  /**
   * @var string $snmpPassword
   * @Column(name="snmpPassword", type="string", length=50, nullable=true)
   */
  protected $snmpPassword;

  /**
   * @var string $snmpVersion
   * @Column(name="snmpVersion", type="string", length=10, nullable=true)
   */
  protected $snmpVersion;

  /**
   * @var string $realm
   * @Column(name="realm", type="string", length=50, nullable=true)
   */
  protected $realm;

  /**
   * @var string $username
   * @Column(name="username", type="string", length=50, nullable=true)
   */
  protected $username;

  /**
   * @var string $pass
   * @Column(name="pass", type="string", length=50, nullable=true)
   */
  protected $pass;

  /**
   * @var string $os
   * @Column(name="os", type="string", length=20, nullable=true)
   */
  protected $os;

  /**
   * @var string $osVersion
   * @Column(name="osVersion", type="string", length=20, nullable=true)
   */
  protected $osVersion;

  /**
   * @var string $sshFingerprint
   * @Column(name="sshFingerprint", type="string", length=60, nullable=true)
   */
  protected $sshFingerprint;

  /**
   * @var integer $l3parent
   * @Column(name="l3parent", type="integer", length=11, nullable=true)
   */
  protected $l3parent;

  /**
   * @var string $l3parentIf
   * @Column(name="l3parentIf", type="string", length=50, nullable=true)
   */
  protected $l3parentIf;

  /**
   * @var APParams
   * @OneToMany(targetEntity="APParams", mappedBy="AP", cascade={"all"})
   */
  protected $Params;

  /**
   * @var APParent
   * @OneToMany(targetEntity="APParent", mappedBy="Parent")
   */
  protected $Parent;

  /**
   * @var APParent
   * @OneToMany(targetEntity="APParent", mappedBy="Children")
   */
  protected $Children;

  /**
   * @var APAntenna
   * @OneToMany(targetEntity="APAntenna", mappedBy="AP", cascade={"all"})
   */
  protected $Antennas;

  /**
   * @var APSwIf
   * @OneToMany(targetEntity="APSwIf", mappedBy="AP", cascade={"all"})
   */
  protected $SwInterfaces;

  /**
   * @var APHw
   * @OneToMany(targetEntity="APHw", mappedBy="AP", cascade={"all"})
   */
  protected $Hardware;

  /**
   * @var APIP
   * @OneToMany(targetEntity="APIP", mappedBy="AP", cascade={"all"})
   */
  protected $IPs;

  /**
   * @var APPort
   * @OneToMany(targetEntity="APPort", mappedBy="AP", cascade={"all"})
   */
  protected $Ports;

  /**
   * @var APPortVlan
   * @OneToMany(targetEntity="APPortVlan", mappedBy="AP", cascade={"all"})
   */
  protected $PortVlans;

  /**
   * @var APCoverage
   * @OneToMany(targetEntity="APCoverage", mappedBy="AP", cascade={"all"})
   */
  protected $Coverages;

  /**
   * @var APRoute
   * @OneToMany(targetEntity="APRoute", mappedBy="AP", cascade={"all"})
   */
  protected $Routes;

  /**
   * @var APService
   * @OneToMany(targetEntity="APService", mappedBy="AP", cascade={"all"})
   */
  protected $Services;

  /**
   * @var APVlan
   * @OneToMany(targetEntity="APVlan", mappedBy="AP", cascade={"all"})
   */
  protected $Vlans;

  /**
   * @var APNetwork
   * @ManyToOne(targetEntity="APNetwork", inversedBy="AP")
   * @JoinColumns({
   *   @JoinColumn(name="APNetwork_ID", referencedColumnName="ID")
   * })
   */
  protected $network;
  
  /**
   * @var APTag
   * @OneToMany(targetEntity="APTag", mappedBy="AP", cascade={"all"})
   */
  protected $Tags;

  /**
   * 
   */
  public function __construct() {
    parent::__construct();
    $this->Params = new ArrayCollection;
    $this->Parent = new ArrayCollection;
    $this->Children = new ArrayCollection;
    $this->Antennas = new ArrayCollection;
    $this->SwInterfaces = new ArrayCollection;
    $this->Hardware = new ArrayCollection;
    $this->IPs = new ArrayCollection;
    $this->Ports = new ArrayCollection;
    $this->PortVlans = new ArrayCollection;
    $this->Coverages = new ArrayCollection;
    $this->Routes = new ArrayCollection;
    $this->Services = new ArrayCollection;
    $this->Vlans = new ArrayCollection;
    $this->Tags = new ArrayCollection;
  }

  /**
   * Get list of parents on Layer 2 of OSI
   * @return array of AP
   */
  public function getL2Parents() {
    // TODO: implement this
    return array();
  }

  /**
   * Get list of parents on Layer 3 of OSI
   * @return array of AP
   */
  public function getL3Parents() {
    // TODO: implement this
    return array();
  }
}
