<?php

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
}
