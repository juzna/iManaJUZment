<?php

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
