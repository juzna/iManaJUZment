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
