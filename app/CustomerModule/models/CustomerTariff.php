<?php

use Doctrine\Common\Collections\ArrayCollection;


/**
 * tariffs of a customer
 *
 * @Table @Entity
 * @ae:links(module="Customer", presenter="dashboard", alias="tariff", common={"add", "edit", "delete"})
 * @hasLifecycleCallbacks
 */
class CustomerTariff extends \ActiveEntity\Entity {
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var Customer $customer
   * @ManyToOne(targetEntity="Customer", inversedBy="Tariffs")
   * @JoinColumn(name="custId", referencedColumnName="custId")
   * @ae:required @ae:immutable
   */
  protected $customer;

  /**
   * @var Tariff $tariff
   * @ManyToOne(targetEntity="Tariff")
   * @JoinColumn(name="tarifId", referencedColumnName="ID")
   * @ae:editable @ae:show
   */
  protected $tariff;

  /**
   * @var string $comment
   * @Column(name="comment", type="string", length=255, nullable=true)
   * @ae:show
   */
  protected $comment;

  /**
   * @var boolean $zakladni
   * @Column(name="zakladni", type="boolean", nullable=false)
   * @ae:immutable @ae:show
   */
  protected $zakladni;

  /**
   * @var boolean $specialniCeny
   * @Column(name="specialniCeny", type="boolean", nullable=false)
   * @ae:show
   */
  protected $specialniCeny;

  /**
   * @var float $mesicniPausal
   * @Column(name="mesicniPausal", type="float", nullable=true)
   */
  protected $mesicniPausal;

  /**
   * @var float $ctvrtletniPausal
   * @Column(name="ctvrtletniPausal", type="float", nullable=true)
   */
  protected $ctvrtletniPausal;

  /**
   * @var float $pololetniPausal
   * @Column(name="pololetniPausal", type="float", nullable=true)
   */
  protected $pololetniPausal;

  /**
   * @var float $rocniPausal
   * @Column(name="rocniPausal", type="float", nullable=true)
   */
  protected $rocniPausal;

  /**
   * @var DateTime $datumOd
   * @Column(name="datumOd", type="date", nullable=false)
   */
  protected $datumOd;

  /**
   * @var DateTime $datumDo
   * @Column(name="datumDo", type="date", nullable=true)
   */
  protected $datumDo;

  /**
   * @var date $predplaceno
   * @Column(name="predplaceno", type="date", nullable=true)
   */
  protected $predplaceno;

  /**
   * @var boolean $aktivni
   * @Column(name="aktivni", type="boolean", nullable=false)
   */
  protected $aktivni;

  /**
   * @var boolean $zaplacenoCele
   * @Column(name="zaplacenoCele", type="boolean", nullable=true)
   */
  protected $zaplacenoCele;

  /**
   * @ManyToMany(targetEntity="CustomerInactivity", mappedBy="tariffs")
   */
  protected $inactivities;

  public function __construct() {
    $this->inactivities = new ArrayCollection;
  }

  /**
   * Update values before saving
   * @prePersist @preUpdate
   */
  public function validateBeforeSaving() {
    $this->zakladni = $this->tariff->zakladni;
    $this->aktivni = empty($this->datumDo) || $this->datumDo->getTimestamp() >= strtotime('now');

    // If it's all paid
    if(!$this->aktivni) {
      // TODO: add check from payments
      $this->zaplacenoCele = false;
    }
    else $this->zaplacenoCele = false;
  }

  /**
   * Get price of this tariff
   * @param int $months
   * @return float
   */
  public function getPrice($months = 1) {
    switch($months) {
      case 1:
        return $this->specialniCeny && $this->mesicniPausal ? $this->mesicniPausal : $this->tariff->mesicniPausal;

      case 3:
        return $this->specialniCeny && $this->ctvrtletniPausal ? $this->ctvrtletniPausal : $this->tariff->ctvrtletniPausal;

      case 6:
        return $this->specialniCeny && $this->ctvrtletniPausal ? $this->ctvrtletniPausal : $this->tariff->ctvrtletniPausal;

      case 12:
        return $this->specialniCeny && $this->rocniPausal ? $this->rocniPausal : $this->tariff->rocniPausal;

      default:
        throw new \InvalidArgumentException("Invalid number of months: $months");
    }
  }

  /**
   * Get all prices of this tariff
   * @return array Keys are num of months, values are prices
   */
  public function getAllPrices() {
    return array(
      1 => $this->getPrice(1),
      3 => $this->getPrice(3),
      6 => $this->getPrice(6),
      12 => $this->getPrice(12),
    );
  }

  /**
   * Get all paymees for this tariff
   * @return array of Paymeetariff
   */
  public function getPaymees() {
    return PaymeeTariff::getRepository()->findBy(array('tariff' => $this->ID));
  }

  /**
   * @return int Number of changes
   */
  public function recalculatePaymees() {
    // TODO: validate all paymees and fix dates
  }

  /**
   * Calculate final prepaid date of this tariff
   * @return DateTime
   * TODO: use also inactivity etc....
   */
  public function calculatePrepaidDate() {
    $date = $this->datumOd;

    foreach($this->getPaymees() as $paymee) {
      $date->add(new \DateInterval('P' . $paymee->months . 'M'));
    }

    return $date;
  }

}
