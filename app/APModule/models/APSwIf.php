<?php


use Doctrine\Common\Collections\ArrayCollection;

/**
 * APSwIf
 *
 * @Table @Entity @ae:Behaviour
 */
class APSwIf extends \ActiveEntity\BehavioralEntity
{
  public static $_behaviours = array(
    'ActiveEntity\\Behaviours\\InetSpeed',
  );

  /**
   * @var integer $ID
   * @Column(name="ID", type="integer")
   * @Id
   * @GeneratedValue(strategy="NONE")
   */
  protected $ID;

  /**
   * @var string $interface
   * @Column(name="interface", type="string", length=50, nullable=false)
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
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="SwInterfaces")
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
