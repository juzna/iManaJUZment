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
 * @entity @table(name="Users")
 */
class User extends ActiveEntity\Entity {
  /**
   * @column(type="integer") @id @generatedValue
   */
  protected $ID;

  /**
   * @column(type="string", length="100")
   */
  protected $username;

  /**
   * @column(type="string", length="10", nullable=true)
   */
  protected $hashMethod;

  /**
   * @column(type="string", length="100", nullable=true)
   */
  protected $password;

  /**
   * @column(type="string", length="100")
   */
  protected $realName;

  /**
   * @column(type="boolean")
   */
  protected $active;

  /**
   * @OneToMany(targetEntity="UserOpenId", mappedBy="user", cascade={"all"})
   */
  protected $openIds;


  public function __construct() {
    $this->openIds = new ArrayCollection;
  }
}
