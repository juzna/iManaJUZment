<?php


use Doctrine\Common\Collections\ArrayCollection;

/**
 * APVlan
 *
 * @Table()
 * @Entity
 */
class APVlan extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer")
   * @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="Vlans")
   * @JoinColumns({
   *   @JoinColumn(name="AP", referencedColumnName="ID")
   * })
   */
  protected $AP;

  /**
   * @var integer $vlan
   * @Column(name="vlan", type="integer")
   */
  protected $vlan;

  /**
   * @var string $description
   * @Column(name="description", type="string", length=255, nullable=true)
   */
  protected $description;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
  
  }
}