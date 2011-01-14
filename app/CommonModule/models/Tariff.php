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
 * Tariff
 *
 * @Table @Entity
 * @ae:links(module="Common", presenter="tarif", alias="tarif", common={"add", "edit", "remove"}, {
 *  @ae:link(title="detail", view="detail", params={"$ID"})
 * })
 */
class Tariff extends \ActiveEntity\Entity {
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var string $nazev
   * @Column(name="nazev", type="string", length=50, nullable=false, unique=true)
   * @ae:link(title="detail", view="detail", params={"$ID"}) @ae:name
   */
  protected $nazev;

  /**
   * @var float $mesicniPausal
   * @Column(name="mesicniPausal", type="float", nullable=false)
   */
  protected $mesicniPausal;

  /**
   * @var float $ctvrtletniPausal
   * @Column(name="ctvrtletniPausal", type="float", nullable=false)
   */
  protected $ctvrtletniPausal;

  /**
   * @var float $pololetniPausal
   * @Column(name="pololetniPausal", type="float", nullable=false)
   */
  protected $pololetniPausal;

  /**
   * @var float $rocniPausal
   * @Column(name="rocniPausal", type="float", nullable=false)
   */
  protected $rocniPausal;

  /**
   * @var string $popis
   * @Column(name="popis", type="string", length=255, nullable=false)
   */
  protected $popis;

  /**
   * @var boolean $posilatFaktury
   * @Column(name="posilatFaktury", type="boolean", nullable=false)
   */
  protected $posilatFaktury;

  /**
   * @var boolean $zakladni
   * @column(type="boolean", nullable=false)
   */
  protected $zakladni;
}