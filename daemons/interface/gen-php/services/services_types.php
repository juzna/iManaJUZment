<?php
namespace Thrift\APos;
/**
 * Autogenerated by Thrift
 *
 * DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
 */
include_once $GLOBALS['THRIFT_ROOT'].'/Thrift.php';

include_once $GLOBALS['THRIFT_ROOT'].'/packages/common/common_types.php';

class ServiceState extends \TBase {
  static $_TSPEC;

  public $ap = null;
  public $serviceName = null;
  public $isRunning = null;
  public $state = null;
  public $moreInfo = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'ap',
          'name' => 'i32',
          'type' => \TType::I32,
          ),
        2 => array(
          'var' => 'serviceName',
          'name' => 'string',
          'type' => \TType::STRING,
          ),
        3 => array(
          'var' => 'isRunning',
          'name' => 'bool',
          'type' => \TType::BOOL,
          ),
        4 => array(
          'var' => 'state',
          'name' => 'string',
          'type' => \TType::STRING,
          ),
        5 => array(
          'var' => 'moreInfo',
          'name' => '',
          'type' => \TType::MAP,
          'ktype' => \TType::STRING,
          'vtype' => \TType::STRING,
          'key' => array(
            'name' => 'string',
            'type' => \TType::STRING,
          ),
          'val' => array(
            'name' => 'string',
            'type' => \TType::STRING,
            ),
          ),
        );
    }
    if (is_array($vals)) {
      parent::__construct(self::$_TSPEC, $vals);
    }
  }

  public function getName() {
    return 'ServiceState';
  }

  public function read($input)
  {
    return $this->_read('ServiceState', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('ServiceState', self::$_TSPEC, $output);
  }
}

class ServiceDescriptor extends \TBase {
  static $_TSPEC;

  public $ap = null;
  public $serviceName = null;
  public $description = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'ap',
          'name' => 'i32',
          'type' => \TType::I32,
          ),
        2 => array(
          'var' => 'serviceName',
          'name' => 'string',
          'type' => \TType::STRING,
          ),
        3 => array(
          'var' => 'description',
          'name' => 'string',
          'type' => \TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      parent::__construct(self::$_TSPEC, $vals);
    }
  }

  public function getName() {
    return 'ServiceDescriptor';
  }

  public function read($input)
  {
    return $this->_read('ServiceDescriptor', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('ServiceDescriptor', self::$_TSPEC, $output);
  }
}

?>
