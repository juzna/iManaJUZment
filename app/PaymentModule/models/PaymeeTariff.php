<?php
/**
 * This file is part of the "iManaJUZment" - complex system for internet service providers
 *
 * Copyright (c) 2005 - 2011 Jan Dolecek (http://juzna.cz)
 *
 * iManaJUZment is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with iManaJUZment.  If not, see <http://www.gnu.org/licenses/gpl.txt>.
 *
 * @license http://www.gnu.org/licenses/gpl.txt
 */


/**
 * @Entity
 */
class PaymeeTariff extends Paymee {

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
   * @var DateTime
   * @Column(type="date")
   */
  protected $dateFrom;

  /**
   * @var DateTime
   * @Column(type="date")
   */
  protected $dateTo;

  /**
   * @param Payment $payment
   * @param float $amount
   * @param CustomerTariff $item
   * @param int $months
   * @param bool $autoDates
   */
  public function __construct(Payment $payment, $amount, CustomerTariff $item, $months, $autoDates = true) {
    parent::__construct($payment, $amount);
    $this->tariff = $item;
    $this->months = $months;

    if($autoDates) {
      $this->dateFrom = $item->calculatePrepaidDate()->add(new \DateInterval("P1D"));
      $this->dateTo = clone $this->dateFrom;
      $this->dateTo->modify("+$months months -1 day");

      // Add to prepaid date
      $item->addMonths($months);
    }
  }

}
