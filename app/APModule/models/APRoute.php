<?php


use Doctrine\Common\Collections\ArrayCollection;

/**
 * APRoute
 *
 * @Table()
 * @Entity
 */
class APRoute extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer")
   * @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="Routes")
   * @JoinColumns({
   *   @JoinColumn(name="AP", referencedColumnName="ID")
   * })
   */
  protected $AP;

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
   * @var string $gateway
   * @Column(name="gateway", type="string", length=15, nullable=false)
   */
  protected $gateway;

  /**
   * @var string $preferedSource
   * @Column(name="preferedSource", type="string", length=15, nullable=true)
   */
  protected $preferedSource;

  /**
   * @var integer $distance
   * @Column(name="distance", type="integer", length=2, nullable=false)
   */
  protected $distance;

  /**
   * @var string $popis
   * @Column(name="popis", type="string", length=255, nullable=true)
   */
  protected $popis;

  /**
   * @var boolean $enabled
   * @Column(name="enabled", type="boolean", nullable=false)
   */
  protected $enabled;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
  
  }
}