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
 * DirectoryAccount
 *
 * @package Model\Common
 * @Table @Entity
 */
class DirectoryAccount extends \ActiveEntity\Entity {
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @ManyToOne(targetEntity="DirectoryEntry", inversedBy="Accounts")
   * @JoinColumn(name="directoryId", referencedColumnName="ID")
   * @ae:immutable @ae:required
   */
  protected $directory;

  /**
   * @var string $predcisli
   * @Column(name="predcisli", type="string", length=10, nullable=false)
   */
  protected $predcisli;

  /**
   * @var string $cislo
   * @Column(name="cislo", type="string", length=10, nullable=false)
   */
  protected $cislo;

  /**
   * @var string $kodBanky
   * @Column(name="kodBanky", type="string", length=4, nullable=false)
   */
  protected $kodBanky;

  /**
   * @var string $poznamka
   * @Column(name="poznamka", type="string", length=255, nullable=true)
   */
  protected $poznamka;

  /**
   * @var bool $active
   * @Column(type="boolean", nullable=false)
   */
  protected $active;
}
