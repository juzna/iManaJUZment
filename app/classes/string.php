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


class String extends \Nette\String {
  public static function camelize($str) {
      return preg_replace('/-([a-z])/e', "strtoupper('\\1')", $str);
  }

  public static function uncamelize($str) {
    return preg_replace('/(?<=[a-z])([A-Z])/e', "'-' . strtolower('\\1')", $str);
  }

  public static function startsWith($haystack, $needle) {
    return strncmp($haystack, $needle, strlen($needle)) === 0;
  }

  static function isFalse($val) {
    return (!$val || $val == 'false' || $val == 'no');
  }

  static function isTrue($val) {
    return $val == 'true' || $val == 'yes' || (is_numeric($val) && $val) || (is_bool($val) && $val);
  }

  /**
  * Get last line of string
  */
  static function getLastLine($text) {
    $pos = strrpos($text, "\n");
    return $pos ? substr($text, $pos + 1) : $text;
  }

  static function getFirstLine($text) {
    $pos = strpos($text, "\n");
    return trim($pos ? substr($text, 0, $pos) : $text, "\r");
  }

  /**
   * Generate string of random characters
   * @param int $len
   * @return string
   */
  static function random__($len) {
    static $z = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    $slen = strlen($z) - 1;
    for($s = ""; $len > 0; $len--) $s .= substr($z, rand(0, $slen), 1);
    return $s;
  }

  /**
   * Implode using printf formatting
   * @param string $glue Glue for implode
   * @param string $format Formatting string - see printf
   * @param array $arr Array of items for imploding
   * @param int $cnt How many parameters need to be passed to printf command (i.e. how many variable syou have)
   *
   * Example:
   *  implode_format(', ', '[%s]', array(...))
   */
  static function implode_format($glue, $format, $arr, $cnt = 1) {
    reset($arr);
    while(list($index, $val) = each($arr)) $arr[$index] = vsprintf($format, array_fill(0, $cnt, $val));
    return implode($glue, $arr);
  }

  /**
   * Translate list of values using a mapping array
   * @param array $table Hashtable
   * @param string|array $index List of items to be translated
   * @param string $col Which column of table should be returned
   * @param bool $returnString If string should be returned; otherwise it return array
   * @return string List of comma delimited values
   */
  static function table_find($table, $index, $col, $returnString = true) {
    $ret = array();
    $indexes = is_arrat($index) ? $index : explode(',', $index);

    foreach($indexes as $index) {
        if($index === '') continue;

        if(isset($table[$index][$col])) $ret[] = $table[$index][$col];
    }

    return $returnString ? implode(',', $ret) : $ret;
  }

  /**
   * Comparator for czech strings
   */
  static function cmp_cz($a, $b) {
    $a = strtolower($a); $b = strtolower($b);
    if($a == $b) return 0;


    $from = array(' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','ch');
    $to = array("e\xFF","s\xFF","c\xFF","r\xFF","z\xFF","y\xFF","a\xFF","i\xFF","e\xFF","u\xFF","t\xFF","h\xFF");

    $a = str_replace($from, $to, $a);
    $b = str_replace($from, $to, $b);

    return ($a > $b) ? 1 : -1;
  }

  /**
   * Two-dimensional explode -> returns a table (array of arrays)
   * @param string $delim1 Oddelovac radku (prvni oddelovac)
   * @param string $delim2 Oddelovac sloupcu (druhy oddelovac)
   * @param string $text Text, jenz ma byt rozdelen
   * @param bool $trim1 Zda se maji osekat prazdne bajty na koncich radku
   * @return array
   */
  static function explode2($delim1, $delim2, $text, $trim1 = true) {
    $ret = array();

    foreach(explode($delim1, $text) as $row) {
      $ret[] = explode($delim2, $trim1 ? trim($row) : $row);
    }

    return $ret;
  }

  /**
   * Two-dimensional implode -> converts a table to a string
   * @param string $delim1 Oddelovac radku (prvni oddelovac)
   * @param string $delim2 Oddelovac sloupcu (druhy oddelovac)
   * @param array $table Tabulka (dvourozmerne pole)
   * @param string $prefix1 String, jenz se ma pripojit na zacatek kazdeho radku
   * @param string $sufix1 String, jenz se ma pripojit na konec kazdeho radku
   * @param string $prefix2 String, jenz se ma pripojit na zacatek kazdeho sloupce
   * @param string $sufix2 String, jenz se ma pripojit na konec kazdeho sloupce
   * @return string
   */
  static function implode2($delim1, $delim2, $table, $prefix1 = '', $sufix1 = '', $prefix2 = '', $sufix2 = '') {
    $ret = '';

    while($table) {
      $row = array_shift($table);

      // Prefix radku
      $ret .= $prefix1;

      // Join columns
      if(empty($prefix2) && empty($sufix2)) $ret .= implode($delim2, $row);
      else while($row) {
        $col = array_shift($row);

        $ret .= $prefix2 . $col . $sufix2;
        if($row) $ret .= $delim2;
      }

      $ret .= $sufix1;

      if($table) $ret .= $delim1;
    }

    return $ret;
  }
}
