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
class BankAccount extends \ActiveEntity\Entity {
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @Column(type="string", length="100")
   */
  protected $name;

  /**
   * @Column(type="string", length="10")
   */
  protected $predcisli;

  /**
   * @Column(type="string", length="20")
   */
  protected $cislo;

  /**
   * @Column(type="string", length="10")
   */
  protected $bankCode;

  /**
   * @Column(type="string", length="20")
   */
  protected $iban;

  /**
   * @Column(type="string", length="255")
   */
  protected $comment;

  /**
   * @Column(type="date", nullable="true")
   */
  protected $activeFrom;

  /**
   * @Column(type="date", nullable="true")
   */
  protected $activeTo;
}
