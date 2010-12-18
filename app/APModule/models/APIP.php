<?php


use Doctrine\Common\Collections\ArrayCollection;

/**
 * APIP
 *
 * @Table @Entity
 * @ae:links(module="AP", presenter="dashboard", alias="ip", common={ "add", "edit", "clone", "delete" })
 */
class APIP extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="IPs")
   * @JoinColumns({
   *   @JoinColumn(name="AP", referencedColumnName="ID")
   * })
   * @ae:immutable @ae:required @ae:show @ae:title("AP#")
   */
  protected $AP;
  
  /**
   * @var string $interface
   * @Column(name="interface", type="string", length=50, nullable=false)
   */
  protected $interface;

  /**
   * @var string $ip
   * @Column(name="ip", type="string", length=15, nullable=false)
   * @ae:title("IP address")
   */
  protected $ip;

  /**
   * @var integer $netmask
   * @Column(name="netmask", type="integer", length=2, nullable=false)
   * @ae:
   */
  protected $netmask;

  /**
   * @var string $description
   * @Column(name="description", type="string", length=255, nullable=true)
   */
  protected $description;

  /**
   * @var bool $enabled
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