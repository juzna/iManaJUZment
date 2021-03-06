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
 * Paymee
 *
 * @package Model\Payment
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