<?php


use Doctrine\Common\Collections\ArrayCollection;

/**
 * AP
 *
 * @Table()
 * @Entity
 */
class AP extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer")
   * @Id
   * @GeneratedValue(strategy="NONE")
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
   * @Column(name="mode", type="enum", nullable=false)
   */
  protected $mode;

  /**
   * @var string $IP
   * @Column(name="IP", type="string", length=15, nullable=false)
   */
  protected $IP;

  /**
   * @var integer $netmask
   * @Column(name="netmask", type="integer", length=2, nullable=false)
   */
  protected $netmask;

  /**
   * @var integer $pvid
   * @Column(name="pvid", type="integer", length=4, nullable=false)
   */
  protected $pvid;

  /**
   * @var boolean $snmpAllowed
   * @Column(name="snmpAllowed", type="boolean", nullable=false)
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
   * @Column(name="os", type="string", length=20, nullable=false)
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
   * @OneToMany(targetEntity="APParams", mappedBy="AP")
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
   * @OneToMany(targetEntity="APAntenna", mappedBy="AP")
   */
  protected $Antennas;

  /**
   * @var APSwIf
   * @OneToMany(targetEntity="APSwIf", mappedBy="AP")
   */
  protected $SwInterfaces;

  /**
   * @var APHw
   * @OneToMany(targetEntity="APHw", mappedBy="AP")
   */
  protected $Hardware;

  /**
   * @var APIP
   * @OneToMany(targetEntity="APIP", mappedBy="AP")
   */
  protected $IPs;

  /**
   * @var APPort
   * @OneToMany(targetEntity="APPort", mappedBy="AP")
   */
  protected $Ports;

  /**
   * @var APPortVlan
   * @OneToMany(targetEntity="APPortVlan", mappedBy="APx")
   */
  protected $PortVlans;

  /**
   * @var APCoverage
   * @OneToMany(targetEntity="APCoverage", mappedBy="AP")
   */
  protected $Coverages;

  /**
   * @var APRoute
   * @OneToMany(targetEntity="APRoute", mappedBy="AP")
   */
  protected $Routes;

  /**
   * @var APService
   * @OneToMany(targetEntity="APService", mappedBy="AP")
   */
  protected $Services;

  /**
   * @var APVlan
   * @OneToMany(targetEntity="APVlan", mappedBy="APx")
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
   * 
   */
  public function __construct()
  {
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
  }
}