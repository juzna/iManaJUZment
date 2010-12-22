<?php

/**
 * @Entity
 */
class UhradaInstalationFee extends Uhrada {
  /**
   * @ManyToOne(targetEntity="CustomerInstalationFee")
   * @JoinColumn
   */
  protected $instalationFee;
}
