<?php



/**
 * TariffSpeed
 *
 * @Table @Entity
 * @ae:Behavioral
 */
class TariffSpeed extends \ActiveEntity\BehavioralEntity
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
   * @ManyToOne(targetEntity="Tariff")
   * @JoinColumn(name="tarifId", referencedColumnName="ID")
   */
  protected $tarif;

  /**
   * @ManyToOne(targetEntity="TariffFlag")
   * @JoinColumn(name="flagId", referencedColumnName="ID")
   */
  protected $flag;

}