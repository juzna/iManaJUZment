<?php

/**
 * @Entity
 */
class CustomerInstalationFee {
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var Customer $customer
   * @ManyToOne(targetEntity="Customer")
   * @JoinColumn(name="custId", referencedColumnName="custId")
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

}
