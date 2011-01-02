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
}
