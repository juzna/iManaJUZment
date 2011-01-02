<?php



/**
 * Paymee
 *
 * @Entity
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="type", type="string")
 * @DiscriminatorMap({
 *   "basic" = "Paymee",
 *   "tariff" = "PaymeeTariff",
 *   "install-fee" = "PaymeeInstalationFee",
 *   "service-fee" = "PaymeeServiceFee"
 * })
 */
class Paymee extends \ActiveEntity\Entity {
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