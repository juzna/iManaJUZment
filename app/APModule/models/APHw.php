<?php


use Doctrine\Common\Collections\ArrayCollection;

/**
 * APHw
 *
 * @Table()
 * @Entity
 */
class APHw extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="Hardware")
   * @JoinColumns({
   *   @JoinColumn(name="AP", referencedColumnName="ID")
   * })
   */
  protected $AP;

  /**
   * @var string $serial
   * @Column(name="serial", type="string", length=50, nullable=true)
   */
  protected $serial;

  /**
   * @var array HwIfs
   * @OneToMany(targetEntity="APHwIf", mappedBy="Hw", cascade={"all"})
   */
  protected $HwIfs;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
  
  }
}