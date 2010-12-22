<?php



/**
 * Adresar
 *
 * @Table @Entity
 */
class Adresar extends \ActiveEntity\Entity {
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
   * @var array of AdresarAdresa List of addresses
   * @OneToMany(targetEntity="AdresarAdresa", mappedBy="adresar", cascade={"all"})
   */
  protected $Addresses;

  /**
   * @var array of AdresarKontakt List of contacts
   * @OneToMany(targetEntity="AdresarKontakt", mappedBy="adresar", cascade={"all"})
   */
  protected $Contacts;

  /**
   * @var array of AdresarUcet List of bank accounts
   * @OneToMany(targetEntity="AdresarUcet", mappedBy="adresar", cascade={"all"})
   */
  protected $Ucty;

  /**
   * @var array of AdresarZalohovyUcet List of backup accounts
   * @OneToMany(targetEntity="AdresarZalohovyUcet", mappedBy="adresar", cascade={"all"})
   */
  protected $UctyZalohove;

  
  function __construct() {
    $this->Addresses = new ArrayCollection;
    $this->Contacts = new ArrayCollection;
    $this->Ucty = new ArrayCollection;
    $this->UctyZalohove = new ArrayCollection;
  }


}