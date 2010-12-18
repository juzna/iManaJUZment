<?php


use Doctrine\Common\Collections\ArrayCollection;

/**
 * APService
 *
 * @Table @Entity
 */
class APService extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="Services")
   * @JoinColumns({
   *   @JoinColumn(name="AP", referencedColumnName="ID")
   * })
   */
  protected $AP;

  /**
   * @var string $state
   * @Column(name="state", type="string", length=20, nullable=false)
   */
  protected $state;

  /**
   * @var string $stateText
   * @Column(name="stateText", type="string", length=100, nullable=true)
   */
  protected $stateText;

  /**
   * @var timestamp $lastCheck
   * @Column(name="lastCheck", type="timestamp", nullable=true)
   */
  protected $lastCheck;

  /**
   * @var APServiceList
   * @ManyToOne(targetEntity="APServiceDefinition")
   * @JoinColumns({
   *   @JoinColumn(name="service", referencedColumnName="code")
   * })
   */
  protected $Definition;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
  
  }
}