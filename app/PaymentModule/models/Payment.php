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


use Doctrine\Common\Collections\ArrayCollection;

/**
 * Platba
 *
 * @Table @Entity
 */
class Payment extends \ActiveEntity\Entity {
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   * @ae:show @ae:link(module="Payment", presenter="dashboard", view="detail", params={"$ID"}, title="Payment detail")
   */
  protected $ID;

  /**
   * @var Customer
   * @ManyToOne(targetEntity="Customer")
   * @JoinColumn(referencedColumnName="custId")
   * @ae:show(helper="name")
   */
  protected $customer;

  /**
   * @ManyToOne(targetEntity="DirectoryEntry")
   * @JoinColumn(name="adresar_id")
   * @ae:show
   */
  protected $directory;

  /**
   * @var PaymentVAT
   * @oneToOne(targetEntity="PaymentVAT", mappedBy="payment")
   */
  protected $vat;


  /**
   * @Column(type="boolean")
   */
  protected $outgoing;

  /**
   * @Column(type="enum")
   * @ae:enumValues("prevod,hotove,slozenka,sipo,jine,dobirka") @ae:show
   */
  protected $method;

  /**
   * @Column(type="date")
   */
  protected $dateAdded;

  /**
   * @Column(type="date")
   * @ae:show
   */
  protected $datePaid;

  /**
   * @Column(type="float")
   * @ae:show
   */
  protected $amount;

  /**
   * @Column(type="string")
   */
  protected $currency;



  /******    Associations *******/

  /**
   * @var array of Paymee
   * @oneToMany(targetEntity="Paymee", mappedBy="payment", cascade={"all"})
   */
  protected $paymees;


  function __construct() {
    $this->paymees = new ArrayCollection;
  }


  function addPaymee(Paymee $paymee) {
    $paymee->payment = $this;
    $this->paymees->add($paymee);
    return $this;
  }

}