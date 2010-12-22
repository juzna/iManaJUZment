<?php



/**
 * Uhrada
 *
 * @Entity
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="type", type="string")
 * @DiscriminatorMap({
 *   "basic" = "Uhrada",
 *   "tariff" = "UhradaTariff",
 *   "install-fee" = "UhradaInstalationFee",
 *   "service-fee" = "UhradaServiceFee"
 * })
 */
class Uhrada extends \ActiveEntity\Entity {
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @ManyToOne(targetEntity="Payment")
   * @JoinColumn()
   */
  protected $payment;

  /**
   * @Column(type="float")
   */
  protected $amount;

  /**
   * @Column(type="string")
   */
  protected $currency;

}