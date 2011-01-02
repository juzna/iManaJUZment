<?php

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
   * @OneToMany(targetEntity="UserOpenId", mappedBy="user")
   */
  protected $openIds;


  public function __construct() {
    $this->openIds = new ArrayCollection;
  }
}
