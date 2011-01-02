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
   * @ManyToOne(targetEntity="Payment", inversedBy="paymees")
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


  public function __construct(Payment $payment, $amount) {
    $this->payment = $payment;
    $this->amount = $amount;
    $this->currency = $payment->currency;
  }
}