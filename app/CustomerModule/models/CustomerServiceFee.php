<?php

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
