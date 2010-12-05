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
   * @var integer $ID
   * @Column(name="ID", type="integer")
   * @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="PortVlans")
   * @JoinColumns({
   *   @JoinColumn(name="AP", referencedColumnName="ID")
   * })
   */
  protected $AP;

  /**
   * @var string $port
   * @Column(name="port", type="string")
   */
  protected $port;

  /**
   * @var integer $vlan
   * @Column(name="vlan", type="integer")
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
   * 
   */
  public function __construct()
  {
    parent::__construct();
  
  }
}