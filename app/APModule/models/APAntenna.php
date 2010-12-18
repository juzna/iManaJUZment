<?php


use Doctrine\Common\Collections\ArrayCollection;

/**
 * APAntenna
 *
 * @Table()
 * @Entity
 */
class APAntenna extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer")
   * @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="Antennas")
   * @JoinColumns({
   *   @JoinColumn(name="AP", referencedColumnName="ID")
   * })
   * @ae:immutable @ae:required @ae:show @ae:title("AP#")
   */
  protected $AP;

  /**
   * @var string $interface
   * @Column(name="interface", type="string", length=50, nullable=false)
   */
  protected $interface;

  /**
   * @var integer $smer
   * @Column(name="smer", type="integer", length=3, nullable=false)
   */
  protected $smer;

  /**
   * @var integer $rozsah
   * @Column(name="rozsah", type="integer", length=3, nullable=false)
   */
  protected $rozsah;

  /**
   * @var integer $dosah
   * @Column(name="dosah", type="integer", length=4, nullable=false)
   */
  protected $dosah;

  /**
   * @var enum $polarita
   * @Column(name="polarita", type="enum", nullable=false)
   */
  protected $polarita;

  /**
   * @var integer $pasmo
   * @Column(name="pasmo", type="integer", length=2, nullable=true)
   */
  protected $pasmo;

  /**
   * @var string $poznamka
   * @Column(name="poznamka", type="string", length=255, nullable=true)
   */
  protected $poznamka;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
  
  }
}