<?php


use Doctrine\Common\Collections\ArrayCollection;

/**
 * APVlan
 *
 * @Table @Entity
 * @ae:links(module="AP", presenter="dashboard", alias="vlan", common={ "add", "edit", "clone", "delete" })
 */
class APVlan extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="Vlans")
   * @JoinColumns({
   *   @JoinColumn(name="AP", referencedColumnName="ID")
   * })
   * @ae:immutable @ae:required @ae:show @ae:title("AP#")
   */
  protected $AP;

  /**
   * @var integer $vlan
   * @Column(name="vlan", type="integer")
   * @ae:show @ae:title("VLAN #")
   */
  protected $vlan;

  /**
   * @var string $description
   * @Column(name="description", type="string", length=255, nullable=true)
   * @ae:show
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