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
class UserOpenId extends ActiveEntity\Entity {
  /**
   * @column(type="integer") @id @generatedValue
   */
  protected $ID;

  /**
   * @manyToOne(targetEntity="User", inversedBy="openIds")
   * @joinColumn
   */
  protected $user;

  /**
   * @column(type="string", length="255")
   */
  protected $identity;

  public function __construct(User $user, $identity) {
    $this->user = $user;
    $this->identity = $identity;

    $user->openIds->add($this);
  }
}
