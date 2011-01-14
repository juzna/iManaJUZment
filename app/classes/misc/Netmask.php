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
 * Work with netmask
 */
class Netmask {
  /** @var int Netmask as a number */
  protected $long = 0;

  /**
   * Parse netmask, can be given in various formats:
   *  - string - 255.255.255.0
   *  - long int - ip address in integer format
   *  - short - like /24
   * @param mixed $netmask
   */
  public function __construct($netmask) {
    if(!isset($netmask)) return;

    if(is_numeric($netmask)) {
      if($netmask >= 0 && $netmask <= 32) $this->setShort($netmask);
      else $this->setLong($netmask);
    }
    else $this->setString($netmask);
  }

  /**
   * Set netmask in short format (like /24)
   * @param int $netmask
   * @return Netmask Provides fluent interface
   */
  public function setShort($netmask) {
    $this->long = bindec(str_repeat('1', $netmask).str_repeat('0', 32 - $netmask));
    return $this;
  }

  /**
   * Set netmask in long number
   * @param int $netmask
   * @return void
   */
  public function setLong($netmask) {
    if(!$this->isValidLong($netmask)) throw new InvalidArgumentException("Not valid netmask in long format: $netmask");
    $this->long = $netmask;
  }

  /**
   * Check if IP address in long format is valid netmask
   * @param int $netmask
   * @return bool
   */
  public function isValidLong($netmask) {
    $binary = decbin($netmask);
    return preg_match('/^1*0*$/', $binary);
  }

  public function setString($netmask) {
    $x = ip2long($netmask);
    if(!$this->isValidLong($x)) throw new InvalidArgumentException("Not valid netmask in string format: $netmask");
    $this->long = $x;
  }

  public function setWildcard($netmask) {
    // TODO:
    throw new NotImplementedException;
  }

  public function getLong() {
    return $this->long;
  }

  public function getShort() {
    return strpos(decbin($this->long), '0');
  }

  public function getString() {
    return long2ip($this->long);
  }


  /****    Static converters    ******/

  static function toLong($x) {
    $n = new self($x);
    return $n->getLong();
  }

  static function toShort($x) {
    $n = new self($x);
    return $n->getShort();
  }

  static function toString($x) {
    $n = new self($x);
    return $n->getString();
  }
}
