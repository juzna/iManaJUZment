<?php

/**
 * @Entity
 */
class PaymeeInstalationFee extends Paymee {
  /**
   * @ManyToOne(targetEntity="CustomerInstalationFee")
   * @JoinColumn
   */
  protected $instalationFee;

  public function __construct(Payment $payment, $amount, CustomerInstalationFee $item) {
    parent::__construct($payment, $amount);
    $this->instalationFee = $item;
  }
}
