<?php


use Doctrine\Common\Collections\ArrayCollection;

/**
 * APIP
 *
 * @Table()
 * @Entity
 */
class APIP extends \ActiveEntity\Entity
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
   * @Column(name="interface", type="string", length=50, nullable=false)
   */
  protected $interface;

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
   * @var string $description
   * @Column(name="description", type="string", length=255, nullable=true)
   */
  protected $description;

  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="IPs")
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
  
  }
}