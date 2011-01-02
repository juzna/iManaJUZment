<?php

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
