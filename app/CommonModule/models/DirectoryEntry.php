<?php



/**
 * DirectoryEntry
 *
 * @Table(name="Directory") @Entity
 */
class DirectoryEntry extends \ActiveEntity\Entity {
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var string $name
   * @Column(name="name", type="string", length=100, nullable=true)
   */
  protected $name;

  /**
   * @var string
   * @Column(type="string")
   */
  protected $ico;

  /**
   * @var string
   * @Column(type="string")
   */
  protected $dic;

  /**
   * @var boolean $jePlatceDph
   * @Column(name="jePlatceDph", type="boolean", nullable=false)
   */
  protected $jePlatceDph;

  /**
   * @var boolean $display
   * @Column(name="display", type="boolean", nullable=false)
   */
  protected $display;



  /*****  Associations  ******/

  /**
   * @var array of DirectoryAddress List of addresses
   * @OneToMany(targetEntity="DirectoryAddress", mappedBy="directory", cascade={"all"})
   */
  protected $Addresses;

  /**
   * @var array of DirectoryContact List of contacts
   * @OneToMany(targetEntity="DirectoryContact", mappedBy="directory", cascade={"all"})
   */
  protected $Contacts;

  /**
   * @var array of DirectoryAccount List of bank accounts
   * @OneToMany(targetEntity="DirectoryAccount", mappedBy="directory", cascade={"all"})
   */
  protected $Accounts;

  /**
   * @var array of DirectoryDepositAccount List of backup accounts
   * @OneToMany(targetEntity="DirectoryDepositAccount", mappedBy="directory", cascade={"all"})
   */
  protected $DepositAccounts;

  
  function __construct() {
    $this->Addresses = new ArrayCollection;
    $this->Contacts = new ArrayCollection;
    $this->Accounts = new ArrayCollection;
    $this->DepositAccounts = new ArrayCollection;
  }


}