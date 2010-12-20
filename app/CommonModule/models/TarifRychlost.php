<?php



/**
 * TarifRychlost
 *
 * @Table @Entity
 * @ae:Behavioral
 */
class TarifRychlost extends \ActiveEntity\BehavioralEntity
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
   * @ManyToOne(targetEntity="Tarif")
   * @JoinColumn(name="tarifId", referencedColumnName="ID")
   */
  protected $tarif;

  /**
   * @ManyToOne(targetEntity="TarifFlag")
   * @JoinColumn(name="flagId", referencedColumnName="ID")
   */
  protected $flag;

}