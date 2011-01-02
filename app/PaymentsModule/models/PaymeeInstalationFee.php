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
}
