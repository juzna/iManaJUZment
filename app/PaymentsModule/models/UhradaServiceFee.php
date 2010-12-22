<?php

/**
 * @Entity
 */
class UhradaServiceFee extends Uhrada {
  /**
   * @ManyToOne(targetEntity="CustomerServiceFee")
   * @JoinColumn
   */
  protected $serviceFee;
}
