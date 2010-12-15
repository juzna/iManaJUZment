<?php


use Doctrine\Common\Collections\ArrayCollection;

/**
 * APSwIf
 *
 * @Table @Entity @ae:Behaviour
 * @ae:links(module="AP", presenter="dashboard", alias="swif", common={ "add", "edit", "clone", "delete" })
 */
class APSwIf extends \ActiveEntity\BehavioralEntity
{
  public static $_behaviours = array(
    'ActiveEntity\\Behaviours\\InetSpeed',
  );

  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="SwInterfaces")
   * @JoinColumns({
   *   @JoinColumn(name="AP", referencedColumnName="ID")
   * })
   * @ae:immutable @ae:show @ae:title("AP#")
   */
  protected $AP;

  /**
   * @var string $interface
   * @Column(name="interface", type="string", length=50, nullable=false)
   * @ae:show
   */
  protected $interface;

  /**
   * @var string $masterInterface
   * @Column(name="masterInterface", type="string", length=50, nullable=true)
   */
  protected $masterInterface;

  /**
   * @var enum $type
   * @Column(name="type", type="enum", nullable=true)
   * @ae:show
   */
  protected $type;

  /**
   * @var boolean $isNet
   * @Column(name="isNet", type="boolean", nullable=false)
   */
  protected $isNet;

  /**
   * @var string $bssid
   * @Column(name="bssid", type="string", length=20, nullable=true)
   */
  protected $bssid;

  /**
   * @var string $essid
   * @Column(name="essid", type="string", length=30, nullable=true)
   * @ae:show
   */
  protected $essid;

  /**
   * @var integer $frequency
   * @Column(name="frequency", type="integer", length=4, nullable=true)
   */
  protected $frequency;

  /**
   * @var boolean $enabled
   * @Column(name="enabled", type="boolean", nullable=false)
   */
  protected $enabled;

  /**
   * @var enum $encType
   * @Column(name="encType", type="enum", nullable=true)
   */
  protected $encType;

  /**
   * @var string $encKey
   * @Column(name="encKey", type="string", length=50, nullable=true)
   */
  protected $encKey;

  /**
   * @var integer $tarifFlag
   * @Column(name="tarifFlag", type="integer", length=11, nullable=false)
   */
  protected $tarifFlag;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
  
  }
}
