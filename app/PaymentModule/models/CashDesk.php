<?php

/**
 * @Entity
 */
class CashDesk extends \ActiveEntity\Entity {
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @Column(type="string", length="100")
   */
  protected $name;
}
