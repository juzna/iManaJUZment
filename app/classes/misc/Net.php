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
 * Functions about network
 */
class Net {

  /**
   * Convert MAC address in misc types to array of hex numbers
   * @return array with 6 items (or false in case of error)
   */
  static function mac2array($mac) {
    $mac = strtoupper(trim($mac));

    if(preg_match('/^([0-9A-F]{2})([0-9A-F]{2})([0-9A-F]{2})([0-9A-F]{2})([0-9A-F]{2})([0-9A-F]{2})$/', $mac, $ret));
    elseif(preg_match('/^([0-9A-F]{2}):([0-9A-F]{2}):([0-9A-F]{2}):([0-9A-F]{2}):([0-9A-F]{2}):([0-9A-F]{2})$/', $mac, $ret));
    elseif(preg_match('/^([0-9A-F]{2})-([0-9A-F]{2})-([0-9A-F]{2})-([0-9A-F]{2})-([0-9A-F]{2})-([0-9A-F]{2})$/', $mac, $ret));
    elseif(preg_match('/^([0-9A-F]{2}) ([0-9A-F]{2}) ([0-9A-F]{2}) ([0-9A-F]{2}) ([0-9A-F]{2}) ([0-9A-F]{2})$/', $mac, $ret));
    elseif(preg_match('/^([0-9A-F]{2})([0-9A-F]{2})-([0-9A-F]{2})([0-9A-F]{2})-([0-9A-F]{2})([0-9A-F]{2})$/', $mac, $ret)); // 3com version
    else return false;

    return array_slice($ret, 1);
  }

  /**
   * Get formatted MAC address
   * @param string $mac
   * @return string
   */
  static function getMac($mac, $delimiter = '') {
    if(!$arr = self::mac2array($mac)) return false;
    return implode($delimiter, $arr);
  }

  /**
  * Get MAC address formatted for 3com switches
   * @param string $mac
   * @return string
  */
  static function getMac2($mac, $delimiter = '-') {
    if(!$arr = self::mac2array($mac)) return false;
    return $arr[0] . $arr[1] . $delimiter . $arr[2] . $arr[3] . $delimiter . $arr[4] . $arr[5];
  }
}
