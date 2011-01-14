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
 * @ae:links(module="Customer", presenter="dashboard", alias="servicefee", common={"add", "edit", "remove"})
 */
class CustomerServiceFee extends \ActiveEntity\Entity {
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var Customer $customer
   * @ManyToOne(targetEntity="Customer")
   * @JoinColumn(name="custId", referencedColumnName="custId")
   * @ae:required @ae:immutable
   */
  protected $customer;

  /**
   * @Column(type="date")
   */
  protected $date;

  /**
   * @Column(type="float")
   */
  protected $amount;

  /**
   * @Column(type="string", length=5)
   */
  protected $currency;

  /**
   * @Column(type="string", length=255)
   */
  protected $comment;

  public function getAmountToBePaid() {
    return $this->amount - $this->getPaid();
  }

  public function getPaid() {
    return (float) em()->createQuery('select sum(p.amount) from PaymeeServiceFee p where p.serviceFee=' . $this->ID)->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_SINGLE_SCALAR);
  }
}
