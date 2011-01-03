<?php
namespace Thrift;
/**
 * Autogenerated by Thrift
 *
 * DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
 */
include_once $GLOBALS['THRIFT_ROOT'].'/Thrift.php';

include_once $GLOBALS['THRIFT_ROOT'].'/packages/common/common_types.php';

$GLOBALS['\Thrift\E_PingMode'] = array(
  'icmp' => 1,
  'arp' => 2,
  'udp' => 3,
);

final class PingMode {
  const icmp = 1;
  const arp = 2;
  const udp = 3;
  static public $__names = array(
    1 => 'icmp',
    2 => 'arp',
    3 => 'udp',
  );
}

$GLOBALS['\Thrift\E_TracerouteMode'] = array(
  'icmp' => 1,
  'tcp' => 2,
  'udp' => 3,
);

final class TracerouteMode {
  const icmp = 1;
  const tcp = 2;
  const udp = 3;
  static public $__names = array(
    1 => 'icmp',
    2 => 'tcp',
    3 => 'udp',
  );
}

class PingResponse extends \TBase {
  static $_TSPEC;

  public $ap = null;
  public $count = null;
  public $times = null;
  public $packetLoss = null;
  public $timeMin = null;
  public $timeAvg = null;
  public $timeMax = null;
  public $mode = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'ap',
          'name' => 'i32',
          'type' => \TType::I32,
          ),
        2 => array(
          'var' => 'count',
          'name' => 'i32',
          'type' => \TType::I32,
          ),
        3 => array(
          'var' => 'times',
          'name' => '',
          'type' => \TType::LST,
          'etype' => \TType::I64,
          'elem' => array(
            'name' => 'i64',
            'type' => \TType::I64,
            ),
          ),
        4 => array(
          'var' => 'packetLoss',
          'name' => 'byte',
          'type' => \TType::BYTE,
          ),
        5 => array(
          'var' => 'timeMin',
          'name' => 'i64',
          'type' => \TType::I64,
          ),
        6 => array(
          'var' => 'timeAvg',
          'name' => 'i64',
          'type' => \TType::I64,
          ),
        7 => array(
          'var' => 'timeMax',
          'name' => 'i64',
          'type' => \TType::I64,
          ),
        8 => array(
          'var' => 'mode',
          'name' => '\Thrift\PingMode',
          'type' => \TType::ENUM,
          'enum' => '\Thrift\E_PingMode',
          ),
        );
    }
    if (is_array($vals)) {
      parent::__construct(self::$_TSPEC, $vals);
    }
  }

  public function getName() {
    return 'PingResponse';
  }

  public function read($input)
  {
    return $this->_read('PingResponse', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('PingResponse', self::$_TSPEC, $output);
  }
}

class TracerouteEntry extends \TBase {
  static $_TSPEC;

  public $position = null;
  public $ip = null;
  public $time = null;
  public $mode = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'position',
          'name' => 'i32',
          'type' => \TType::I32,
          ),
        2 => array(
          'var' => 'ip',
          'name' => 'string',
          'type' => \TType::STRING,
          ),
        3 => array(
          'var' => 'time',
          'name' => 'i64',
          'type' => \TType::I64,
          ),
        4 => array(
          'var' => 'mode',
          'name' => '\Thrift\TracerouteMode',
          'type' => \TType::ENUM,
          'enum' => '\Thrift\E_TracerouteMode',
          ),
        );
    }
    if (is_array($vals)) {
      parent::__construct(self::$_TSPEC, $vals);
    }
  }

  public function getName() {
    return 'TracerouteEntry';
  }

  public function read($input)
  {
    return $this->_read('TracerouteEntry', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('TracerouteEntry', self::$_TSPEC, $output);
  }
}

class TracerouteResponse extends \TBase {
  static $_TSPEC;

  public $ap = null;
  public $length = null;
  public $responses = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'ap',
          'name' => 'i32',
          'type' => \TType::I32,
          ),
        2 => array(
          'var' => 'length',
          'name' => 'i32',
          'type' => \TType::I32,
          ),
        3 => array(
          'var' => 'responses',
          'name' => '',
          'type' => \TType::LST,
          'etype' => \TType::STRUCT,
          'elem' => array(
            'name' => '\Thrift\TracerouteEntry',
            'type' => \TType::STRUCT,
            'class' => '\Thrift\TracerouteEntry',
            ),
          ),
        );
    }
    if (is_array($vals)) {
      parent::__construct(self::$_TSPEC, $vals);
    }
  }

  public function getName() {
    return 'TracerouteResponse';
  }

  public function read($input)
  {
    return $this->_read('TracerouteResponse', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('TracerouteResponse', self::$_TSPEC, $output);
  }
}

?>
