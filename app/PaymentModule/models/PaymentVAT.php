<?php

/**
 * @entity
 */
class PaymentVAT extends \ActiveEntity\Entity {
  /**
   * @var int $ID
   * @column(type="integer") @id @generatedValue
   */
  protected $ID;

  /**
   * @var Payment
   * @OneToOne(targetEntity="Payment", inversedBy="vat")
   * @joinColumn
   */
  protected $payment;

  /**
   * @column(type="float")
   */
  protected $amount0;



  /**
   * @column(type="float")
   */
  protected $amount1;

  /**
   * @column(type="integer")
   */
  protected $rate1;

  /**
   * @column(type="float")
   */
  protected $vat1;



  /**
   * @column(type="float")
   */
  protected $amount2;

  /**
   * @column(type="integer")
   */
  protected $rate2;

  /**
   * @column(type="float")
   */
  protected $vat2;

  /**
   * @var boolean Allow automatic calculation of VAT
   * @column(type="boolean", nullable=false)
   */
  protected $auto;
}
