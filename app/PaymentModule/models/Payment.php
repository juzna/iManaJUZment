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