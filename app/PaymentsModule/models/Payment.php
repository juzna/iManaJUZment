<?php

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
   */
  protected $ID;

  /**
   * @var Customer
   * @ManyToOne(targetEntity="Customer")
   * @JoinColumn(referencedColumnName="custId")
   */
  protected $customer;

  /**
   * @ManyToOne(targetEntity="Adresar")
   * @JoinColumn
   */
  protected $adresar;


  /**
   * @Column(type="boolean")
   */
  protected $outgoing;

  /**
   * @Column(type="enum")
   * @ae:enumValues("prevod,hotove,slozenka,sipo,jine,dobirka")
   */
  protected $method;

  /**
   * @Column(type="date")
   */
  protected $dateAdded;

  /**
   * @Column(type="date")
   */
  protected $datePaid;

  /**
   * @Column(type="float")
   */
  protected $ammount;

  /**
   * @Column(type="string")
   */
  protected $currency;



  /******    Associations *******/



  function __construct() {
    $this->Uhrady = new ArrayCollection;
  }
  

}