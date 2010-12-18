<?php


use Doctrine\Common\Collections\ArrayCollection;

/**
 * APHwIf
 *
 * @Table()
 * @Entity
 */
class APHwIf extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var Hw
   * @ManyToOne(targetEntity="APHw", inversedBy="HwIfs")
   * @JoinColumns({
   *   @JoinColumn(name="APHw", referencedColumnName="ID")
   * })
   */
  protected $Hw;

  /**
   * @var string $interface
   * @Column(name="interface", type="string", length=50, nullable=false)
   */
  protected $interface;

  /**
   * @var string $mac
   * @Column(name="mac", type="string", length=20, nullable=true)
   */
  protected $mac;

  /**
   * @var enum $typ
   * @Column(name="typ", type="enum", nullable=false)
   */
  protected $typ;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
  
  }
}