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
 * APParent
 *
 * @Table @Entity
 */
class APParent extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="Children")
   * @JoinColumns({
   *   @JoinColumn(name="parentAP", referencedColumnName="ID")
   * })
   */
  protected $Parent;

  /**
   * @var string $parentInterface
   * @Column(name="parentInterface", type="string", length=50, nullable=true)
   */
  protected $parentInterface;

  /**
   * @var string $parentPort
   * @Column(name="parentPort", type="string", length=50, nullable=true)
   */
  protected $parentPort;

  /**
   * @var integer $parentVlan
   * @Column(name="parentVlan", type="integer", length=4, nullable=false)
   */
  protected $parentVlan;




  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="Parents")
   * @JoinColumns({
   *   @JoinColumn(name="childAP", referencedColumnName="ID")
   * })
   */
  protected $Child;

  /**
   * @var string $childInterface
   * @Column(name="childInterface", type="string", length=50, nullable=true)
   */
  protected $childInterface;

  /**
   * @var string $childPort
   * @Column(name="childPort", type="string", length=50, nullable=true)
   */
  protected $childPort;

  /**
   * @var integer $childVlan
   * @Column(name="childVlan", type="integer", length=4, nullable=false)
   */
  protected $childVlan;



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