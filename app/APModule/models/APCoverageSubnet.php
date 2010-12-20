<?php


use Doctrine\Common\Collections\ArrayCollection;

/**
 * APPokrytiSubnet
 *
 * @Table @Entity
 * @ae:links(module="AP", presenter="dashboard", alias="coverageSubnet", common={ "add", "edit", "clone", "delete" })
 */
class APCoverageSubnet extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var APCoverage
   * @ManyToOne(targetEntity="APCoverage", inversedBy="Subnets")
   * @JoinColumns({
   *   @JoinColumn(name="coverage", referencedColumnName="ID")
   * })
   * @ae:immutable @ae:required
   */
  protected $Coverage;

  /**
   * @var string $ip
   * @Column(name="ip", type="string", length=15, nullable=false)
   * @ae:title("Subnet address")
   */
  protected $ip;

  /**
   * @var integer $netmask
   * @Column(name="netmask", type="integer", length=2, nullable=false)
   */
  protected $netmask;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
  }
}