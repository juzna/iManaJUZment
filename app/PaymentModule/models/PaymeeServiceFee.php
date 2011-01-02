<?php

/**
 * @Entity
 */
class PaymeeServiceFee extends Paymee {
  /**
   * @ManyToOne(targetEntity="CustomerServiceFee")
   * @JoinColumn
   */
  protected $serviceFee;

  public function __construct(Payment $payment, $amount, CustomerServiceFee $item) {
    parent::__construct($payment, $amount);
    $this->serviceFee = $item;
  }
}
