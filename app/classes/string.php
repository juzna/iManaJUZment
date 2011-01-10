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
}
