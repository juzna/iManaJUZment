<?php


use Doctrine\Common\Collections\ArrayCollection;

/**
 * APPokrytiSubnet
 *
 * @Table @Entity
 */
class APCoverageSubnet extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer")
   * @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var string $ip
   * @Column(name="ip", type="string", length=15, nullable=false)
   */
  protected $ip;

  /**
   * @var integer $netmask
   * @Column(name="netmask", type="integer", length=2, nullable=false)
   */
  protected $netmask;

  /**
   * @var APCoverage
   * @ManyToOne(targetEntity="APCoverage", inversedBy="Subnets")
   * @JoinColumns({
   *   @JoinColumn(name="coverage", referencedColumnName="ID")
   * })
   */
  protected $Coverage;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
  }
}