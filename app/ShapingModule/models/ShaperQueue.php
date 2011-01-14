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
 * @entity
 */
class ShaperQueue extends \ActiveEntity\Entity {
  /**
   * @column(type="integer") @id @generatedValue
   */
  protected $ID;

  /**
   * @var Shaper
   * @manyToOne(targetEntity="Shaper", inversedBy="queues")
   * @joinColumn
   */
  protected $shaper;

  /**
   * @var ShaperQueue
   * @manyToOne(targetEntity="ShaperQueue", inversedBy="children")
   * @joinColumn
   */
  protected $parent;

  /**
   * @var array of ShaperQueue
   * @oneToMany(targetEntity="ShaperQueue", mappedBy="parent")
   */
  protected $children;

  /**
   * @var AP
   * @manyToOne(targetEntity="AP")
   * @joinColumn
   */
  protected $ap;

  /**
   * @column(type="string", length="100")
   */
  protected $interface;


  /******  Transmit speed  *******/

  /**
   * @var boolean
   * @column(type="boolean")
   */
  protected $txAllow;

  /**
   * @var boolean
   * @column(type="boolean")
   */
  protected $txCustomers;

  /**
   * @column(type="string")
   */
  protected $txMin;

  /**
   * @column(type="string")
   */
  protected $txMax;

  /**
   * @column(type="string")
   */
  protected $txBurst;

  /**
   * @column(type="string")
   */
  protected $txBurstTime;

  /**
   * @column(type="string")
   */
  protected $txTreshold;

  /**
   * @column(type="integer")
   */
  protected $txPriority = 7;




  /******  Receive speed  *******/

  /**
   * @var boolean
   * @column(type="boolean")
   */
  protected $rxAllow;

  /**
   * @var boolean
   * @column(type="boolean")
   */
  protected $rxCustomers;

  /**
   * @column(type="string")
   */
  protected $rxMin;

  /**
   * @column(type="string")
   */
  protected $rxMax;

  /**
   * @column(type="string")
   */
  protected $rxBurst;

  /**
   * @column(type="string")
   */
  protected $rxBurstTime;

  /**
   * @column(type="string")
   */
  protected $rxTreshold;

  /**
   * @column(type="integer")
   */
  protected $rxPriority = 7;
}
