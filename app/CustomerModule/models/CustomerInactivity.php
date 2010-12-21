<?php

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Inactivity of a customer
 *
 * @Table @Entity
 */
class CustomerInactivity extends \ActiveEntity\Entity {
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var Customer $customer
   * @ManyToOne(targetEntity="Customer", inversedBy="Inactivities")
   * @JoinColumn(name="custId", referencedColumnName="custId")
   */
  protected $customer;

  /**
   * @var date $datumOd
   * @Column(name="datumOd", type="date", nullable=false)
   */
  protected $datumOd;

  /**
   * @var date $datumDo
   * @Column(name="datumDo", type="date", nullable=true)
   */
  protected $datumDo;

  /**
   * @var string $reason
   * @Column(name="reason", type="string", length=255, nullable=false)
   */
  protected $reason;

  /**
   * @ManyToMany(targetEntity="CustomerTariff", inversedBy="inactivities")
   * @JoinTable(name="CustomerInactivityTariff",
   *   joinColumns={ @JoinColumn(name="inactivityId", referencedColumnName="ID") },
   *   inverseJoinColumns={ @JoinColumn(name="customerTariffId", referencedColumnName="ID") }
   * )
   */
  protected $tariffs;

  public function __construct() {
    $this->tariffs = new ArrayCollection;
  }
}