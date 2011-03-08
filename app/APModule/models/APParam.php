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



use Doctrine\Common\Collections\ArrayCollection;

/**
 * APParam
 *
 * @package Model\AP
 * @Table @Entity
 */
class APParams extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="Params")
   * @JoinColumns({
   *   @JoinColumn(name="AP", referencedColumnName="ID")
   * })
   */
  protected $AP;

  /**
   * @var string $name
   * @Column(name="name", type="string", length=50, nullable=true)
   */
  protected $name;

  /**
   * @var string $value
   * @Column(name="value", type="string", length=50, nullable=true)
   */
  protected $value;

  /**
   * @var string $comment
   * @Column(name="comment", type="string", length=255, nullable=true)
   */
  protected $comment;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
  
  }
}