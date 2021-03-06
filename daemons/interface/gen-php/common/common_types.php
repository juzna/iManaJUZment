<?php
namespace Thrift;
/**
 * Autogenerated by Thrift
 *
 * DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
 */
include_once $GLOBALS['THRIFT_ROOT'].'/Thrift.php';


class coordinates extends \TBase {
  static $_TSPEC;

  public $posX = null;
  public $posY = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'posX',
          'name' => 'i64',
          'type' => \TType::I64,
          ),
        2 => array(
          'var' => 'posY',
          'name' => 'i64',
          'type' => \TType::I64,
          ),
        );
    }
    if (is_array($vals)) {
      parent::__construct(self::$_TSPEC, $vals);
    }
  }

  public function getName() {
    return 'coordinates';
  }

  public function read($input)
  {
    return $this->_read('coordinates', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('coordinates', self::$_TSPEC, $output);
  }
}

?>
