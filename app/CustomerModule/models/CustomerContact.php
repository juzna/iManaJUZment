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
 * Contact defails for a customer
 *
 * @package Model\Customer
 * @Table @Entity
 */
class CustomerContact extends \ActiveEntity\Entity {
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var Customer $customer
   * @ManyToOne(targetEntity="Customer", inversedBy="Contacts")
   * @JoinColumn(name="custId", referencedColumnName="custId")
   * @ae:required @ae:immutable
   */
  protected $customer;

  /**
   * @var string $type
   * @Column(name="type", type="string", length=10, nullable=false)
   */
  protected $type;

  /**
   * @var string $value
   * @Column(name="value", type="string", length=100, nullable=false)
   */
  protected $value;

  /**
   * @var string $comment
   * @Column(name="comment", type="string", length=255, nullable=true)
   */
  protected $comment;
}
