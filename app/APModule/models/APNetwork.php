<?php


use Doctrine\Common\Collections\ArrayCollection;

/**
 * APNetwork
 *
 * @Table()
 * @Entity
 */
class APNetwork extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var string $name
   * @Column(name="name", type="string", length=50, nullable=false)
   */
  protected $name;

  /**
   * @var string $description
   * @Column(name="description", type="string", length=255, nullable=true)
   */
  protected $description;

  /**
   * @var AP
   * @OneToMany(targetEntity="AP", mappedBy="network")
   */
  protected $AP;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
    $this->AP = new ArrayCollection;
  }
}