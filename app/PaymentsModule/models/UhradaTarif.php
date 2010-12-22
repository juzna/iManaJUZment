<?php

/**
 * @Entity
 */
class UhradaTariff extends Uhrada {

  /**
   * @ManyToOne(targetEntity="CustomerTariff")
   * @JoinColumn
   */
  protected $tariff;

  /**
   * @Column(type="integer") @ae:default(1)
   */
  protected $months;

  /**
   * @Column(type="date")
   */
  protected $dateFrom;

  /**
   * @Column(type="date")
   */
  protected $dateTo;
}
