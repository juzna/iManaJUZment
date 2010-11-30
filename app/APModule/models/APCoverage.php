<?php


use Doctrine\Common\Collections\ArrayCollection;

/**
 * APCoverage
 *
 * @Table()
 * @Entity
 */
class APCoverage extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer")
   * @Id
   * @GeneratedValue(strategy="NONE")
   */
  protected $ID;

  /**
   * @var string $interface
   * @Column(name="interface", type="string", length=50, nullable=true)
   */
  protected $interface;

  /**
   * @var integer $vlan
   * @Column(name="vlan", type="integer", length=4, nullable=true)
   */
  protected $vlan;

  /**
   * @var integer $adresa
   * @Column(name="adresa", type="integer", length=11, nullable=false)
   */
  protected $adresa;

  /**
   * @var string $poznamka
   * @Column(name="poznamka", type="string", length=255, nullable=true)
   */
  protected $poznamka;

  /**
   * @var integer $doporuceni
   * @Column(name="doporuceni", type="integer", length=1, nullable=false)
   */
  protected $doporuceni;

  /**
   * @var APPokrytiSubnet
   * @OneToMany(targetEntity="APPokrytiSubnet", mappedBy="Coverage")
   */
  protected $Subnets;

  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="Coverages")
   * @JoinColumns({
   *   @JoinColumn(name="AP", referencedColumnName="ID")
   * })
   */
  protected $AP;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
    $this->Subnets = new ArrayCollection;
  }
}