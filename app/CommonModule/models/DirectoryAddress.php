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
 * DirectoryAddress
 *
 * @package Model\Common
 * @Table @Entity
 */
class DirectoryAddress extends \ActiveEntity\Entity {
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @ManyToOne(targetEntity="DirectoryEntry", inversedBy="Addresses")
   * @JoinColumn(name="directoryId", referencedColumnName="ID")
   * @ae:immutable @ae:required
   */
  protected $directory;



  /*****   flags  ******/

  /**
   * @var boolean $isOdberna
   * @Column(name="isOdberna", type="boolean", nullable=false)
   */
  protected $isOdberna;

  /**
   * @var boolean $isFakturacni
   * @Column(name="isFakturacni", type="boolean", nullable=false)
   */
  protected $isFakturacni;

  /**
   * @var boolean $isKorespondencni
   * @Column(name="isKorespondencni", type="boolean", nullable=false)
   */
  protected $isKorespondencni;

  /**
   * @var string $popis
   * @Column(name="popis", type="string", length=255, nullable=true)
   */
  protected $popis;




  /******  personal info *****/

  /**
   * @var string $firma
   * @Column(name="firma", type="string", length=100, nullable=true)
   */
  protected $firma;

  /**
   * @var string $firma2
   * @Column(name="firma2", type="string", length=100, nullable=true)
   */
  protected $firma2;

  /**
   * @var string $titulPred
   * @Column(name="titulPred", type="string", length=50, nullable=true)
   */
  protected $titulPred;

  /**
   * @var string $jmeno
   * @Column(name="jmeno", type="string", length=50, nullable=true)
   */
  protected $jmeno;

  /**
   * @var string $druheJmeno
   * @Column(name="druheJmeno", type="string", length=50, nullable=true)
   */
  protected $druheJmeno;

  /**
   * @var string $prijmeni
   * @Column(name="prijmeni", type="string", length=50, nullable=true)
   */
  protected $prijmeni;

  /**
   * @var string $druhePrijmeni
   * @Column(name="druhePrijmeni", type="string", length=50, nullable=true)
   */
  protected $druhePrijmeni;

  /**
   * @var string $titulZa
   * @Column(name="titulZa", type="string", length=50, nullable=true)
   */
  protected $titulZa;




  /********  adresa  ********/

  /**
   * @var string $ulice
   * @Column(name="ulice", type="string", length=50, nullable=true)
   */
  protected $ulice;

  /**
   * @var string $cisloPopisne
   * @Column(name="cisloPopisne", type="string", length=20, nullable=true)
   */
  protected $cisloPopisne;

  /**
   * @var string $mesto
   * @Column(name="mesto", type="string", length=50, nullable=true)
   */
  protected $mesto;

  /**
   * @var string $PSC Post code
   * @Column(name="PSC", type="string", length=10, nullable=true)
   */
  protected $PSC;

  /**
   * @var integer $uir_objekt
   * @Column(name="uir_objekt", type="integer", length=11, nullable=true)
   */
  protected $uir_objekt;



  /*********   misc   ******/

  /**
   * @var string $ICO
   * @Column(name="ICO", type="string", length=20, nullable=true)
   */
  protected $ICO;

  /**
   * @var string $DIC
   * @Column(name="DIC", type="string", length=20, nullable=true)
   */
  protected $DIC;

  /**
   * @var string $poznamka
   * @Column(name="poznamka", type="string", length=255, nullable=true)
   */
  protected $poznamka;

  /**
   * @var string $rodneCislo
   * @Column(name="rodneCislo", type="string", length=20, nullable=true)
   */
  protected $rodneCislo;

  /**
   * @var string $datumNarozeni
   * @Column(name="datumNarozeni", type="string", length=20, nullable=true)
   */
  protected $datumNarozeni;

}