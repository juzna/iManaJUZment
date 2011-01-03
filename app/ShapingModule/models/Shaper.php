<?php

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @entity
 */
class Shaper extends \ActiveEntity\Entity {
  /**
   * @column(type="integer") @id
   */
  protected $ID;

  /**
   * @var AP
   * @oneToOne(targetEntity="AP", inversedBy="shaper")
   * @joinColumn
   */
  protected $ap;

  /**
   * @column(type="boolean")
   */
  protected $allowTxZbytek;

  /**
   * @column(type="boolean")
   */
  protected $allowRxZbytek;


  /**
   * @oneToMany(targetEntity="ShaperQueue", mappedBy="shaper")
   */
  protected $queues;


  public function __construct() {
    $this->queues = new ArrayColleciton;
  }
}