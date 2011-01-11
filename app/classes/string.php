<?php

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
  static function randchar($len) {
    static $z = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    $slen = strlen($z) - 1;
    for($s = ""; $len > 0; $len--) $s .= substr($z, rand(0, $slen), 1);
    return $s;
  }

}
