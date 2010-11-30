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
   * @Column(name="ID", type="integer")
   * @Id
   * @GeneratedValue(strategy="NONE")
   */
  protected $ID;

  /**
   * @var string $serial
   * @Column(name="serial", type="string", length=50, nullable=true)
   */
  protected $serial;

  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="Hardware")
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