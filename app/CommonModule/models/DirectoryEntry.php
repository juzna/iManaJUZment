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
 * DirectoryEntry
 *
 * @package Model\Common
 * @Table(name="Directory") @Entity
 */
class DirectoryEntry extends \ActiveEntity\Entity {
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var string $name
   * @Column(name="name", type="string", length=100, nullable=true)
   */
  protected $name;

  /**
   * @var string
   * @Column(type="string")
   */
  protected $ico;

  /**
   * @var string
   * @Column(type="string")
   */
  protected $dic;

  /**
   * @var boolean $jePlatceDph
   * @Column(name="jePlatceDph", type="boolean", nullable=false)
   */
  protected $jePlatceDph;

  /**
   * @var boolean $display
   * @Column(name="display", type="boolean", nullable=false)
   */
  protected $display;



  /*****  Associations  ******/

  /**
   * @var array of DirectoryAddress List of addresses
   * @OneToMany(targetEntity="DirectoryAddress", mappedBy="directory", cascade={"all"})
   */
  protected $Addresses;

  /**
   * @var array of DirectoryContact List of contacts
   * @OneToMany(targetEntity="DirectoryContact", mappedBy="directory", cascade={"all"})
   */
  protected $Contacts;

  /**
   * @var array of DirectoryAccount List of bank accounts
   * @OneToMany(targetEntity="DirectoryAccount", mappedBy="directory", cascade={"all"})
   */
  protected $Accounts;

  /**
   * @var array of DirectoryDepositAccount List of backup accounts
   * @OneToMany(targetEntity="DirectoryDepositAccount", mappedBy="directory", cascade={"all"})
   */
  protected $DepositAccounts;

  
  function __construct() {
    $this->Addresses = new ArrayCollection;
    $this->Contacts = new ArrayCollection;
    $this->Accounts = new ArrayCollection;
    $this->DepositAccounts = new ArrayCollection;
  }


}