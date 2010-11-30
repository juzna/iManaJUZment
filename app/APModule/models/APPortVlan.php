<?php


use Doctrine\Common\Collections\ArrayCollection;

/**
 * APPortVlan
 *
 * @Table()
 * @Entity
 */
class APPortVlan extends \ActiveEntity\Entity
{
  /**
   * @var integer $AP
   * @Column(name="AP", type="integer")
   * @Id
   * @GeneratedValue(strategy="NONE")
   */
  protected $AP;

  /**
   * @var string $port
   * @Column(name="port", type="string")
   * @Id
   * @GeneratedValue(strategy="NONE")
   */
  protected $port;

  /**
   * @var integer $vlan
   * @Column(name="vlan", type="integer")
   * @Id
   * @GeneratedValue(strategy="NONE")
   */
  protected $vlan;

  /**
   * @var boolean $tagged
   * @Column(name="tagged", type="boolean", nullable=false)
   */
  protected $tagged;

  /**
   * @var boolean $pvid
   * @Column(name="pvid", type="boolean", nullable=false)
   */
  protected $pvid;

  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="PortVlans")
   * @JoinColumns({
   *   @JoinColumn(name="APx_id", referencedColumnName="id")
   * })
   */
  protected $APx;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
  
  }
}