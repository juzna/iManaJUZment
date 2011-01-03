<?php
namespace Thrift\tutorial;
/**
 * Autogenerated by Thrift
 *
 * DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
 */
include_once $GLOBALS['THRIFT_ROOT'].'/Thrift.php';

include_once $GLOBALS['THRIFT_ROOT'].'/packages/tutorial/tutorial_types.php';
include_once $GLOBALS['THRIFT_ROOT'].'/packages/shared/SharedService.php';

interface CalculatorIf extends \Thrift\SharedServiceIf {
  public function ping();
  public function add($num1, $num2);
  public function calculate($logid, $w);
  public function zip();
}

class CalculatorClient extends \Thrift\SharedServiceClient implements \Thrift\tutorial\CalculatorIf {
  public function __construct($input, $output=null) {
    parent::__construct($input, $output);
  }

  public function ping()
  {
    $this->send_ping();
    $this->recv_ping();
  }

  public function send_ping()
  {
    $args = new \Thrift\tutorial\Calculator_ping_args();
    $bin_accel = ($this->output_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($this->output_, 'ping', \TMessageType::CALL, $args, $this->seqid_, $this->output_->isStrictWrite());
    }
    else
    {
      $this->output_->writeMessageBegin('ping', \TMessageType::CALL, $this->seqid_);
      $args->write($this->output_);
      $this->output_->writeMessageEnd();
      $this->output_->getTransport()->flush();
    }
  }

  public function recv_ping()
  {
    $bin_accel = ($this->input_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_read_binary');
    if ($bin_accel) $result = thrift_protocol_read_binary($this->input_, '\Thrift\tutorial\Calculator_ping_result', $this->input_->isStrictRead());
    else
    {
      $rseqid = 0;
      $fname = null;
      $mtype = 0;

      $this->input_->readMessageBegin($fname, $mtype, $rseqid);
      if ($mtype == \TMessageType::EXCEPTION) {
        $x = new \TApplicationException();
        $x->read($this->input_);
        $this->input_->readMessageEnd();
        throw $x;
      }
      $result = new \Thrift\tutorial\Calculator_ping_result();
      $result->read($this->input_);
      $this->input_->readMessageEnd();
    }
    return;
  }

  public function add($num1, $num2)
  {
    $this->send_add($num1, $num2);
    return $this->recv_add();
  }

  public function send_add($num1, $num2)
  {
    $args = new \Thrift\tutorial\Calculator_add_args();
    $args->num1 = $num1;
    $args->num2 = $num2;
    $bin_accel = ($this->output_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($this->output_, 'add', \TMessageType::CALL, $args, $this->seqid_, $this->output_->isStrictWrite());
    }
    else
    {
      $this->output_->writeMessageBegin('add', \TMessageType::CALL, $this->seqid_);
      $args->write($this->output_);
      $this->output_->writeMessageEnd();
      $this->output_->getTransport()->flush();
    }
  }

  public function recv_add()
  {
    $bin_accel = ($this->input_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_read_binary');
    if ($bin_accel) $result = thrift_protocol_read_binary($this->input_, '\Thrift\tutorial\Calculator_add_result', $this->input_->isStrictRead());
    else
    {
      $rseqid = 0;
      $fname = null;
      $mtype = 0;

      $this->input_->readMessageBegin($fname, $mtype, $rseqid);
      if ($mtype == \TMessageType::EXCEPTION) {
        $x = new \TApplicationException();
        $x->read($this->input_);
        $this->input_->readMessageEnd();
        throw $x;
      }
      $result = new \Thrift\tutorial\Calculator_add_result();
      $result->read($this->input_);
      $this->input_->readMessageEnd();
    }
    if ($result->success !== null) {
      return $result->success;
    }
    throw new \Exception("add failed: unknown result");
  }

  public function calculate($logid, $w)
  {
    $this->send_calculate($logid, $w);
    return $this->recv_calculate();
  }

  public function send_calculate($logid, $w)
  {
    $args = new \Thrift\tutorial\Calculator_calculate_args();
    $args->logid = $logid;
    $args->w = $w;
    $bin_accel = ($this->output_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($this->output_, 'calculate', \TMessageType::CALL, $args, $this->seqid_, $this->output_->isStrictWrite());
    }
    else
    {
      $this->output_->writeMessageBegin('calculate', \TMessageType::CALL, $this->seqid_);
      $args->write($this->output_);
      $this->output_->writeMessageEnd();
      $this->output_->getTransport()->flush();
    }
  }

  public function recv_calculate()
  {
    $bin_accel = ($this->input_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_read_binary');
    if ($bin_accel) $result = thrift_protocol_read_binary($this->input_, '\Thrift\tutorial\Calculator_calculate_result', $this->input_->isStrictRead());
    else
    {
      $rseqid = 0;
      $fname = null;
      $mtype = 0;

      $this->input_->readMessageBegin($fname, $mtype, $rseqid);
      if ($mtype == \TMessageType::EXCEPTION) {
        $x = new \TApplicationException();
        $x->read($this->input_);
        $this->input_->readMessageEnd();
        throw $x;
      }
      $result = new \Thrift\tutorial\Calculator_calculate_result();
      $result->read($this->input_);
      $this->input_->readMessageEnd();
    }
    if ($result->success !== null) {
      return $result->success;
    }
    if ($result->ouch !== null) {
      throw $result->ouch;
    }
    throw new \Exception("calculate failed: unknown result");
  }

  public function zip()
  {
    $this->send_zip();
  }

  public function send_zip()
  {
    $args = new \Thrift\tutorial\Calculator_zip_args();
    $bin_accel = ($this->output_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($this->output_, 'zip', \TMessageType::CALL, $args, $this->seqid_, $this->output_->isStrictWrite());
    }
    else
    {
      $this->output_->writeMessageBegin('zip', \TMessageType::CALL, $this->seqid_);
      $args->write($this->output_);
      $this->output_->writeMessageEnd();
      $this->output_->getTransport()->flush();
    }
  }
}

// HELPER FUNCTIONS AND STRUCTURES

class Calculator_ping_args extends \TBase {
  static $_TSPEC;


  public function __construct() {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        );
    }
  }

  public function getName() {
    return 'Calculator_ping_args';
  }

  public function read($input)
  {
    return $this->_read('Calculator_ping_args', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Calculator_ping_args', self::$_TSPEC, $output);
  }
}

class Calculator_ping_result extends \TBase {
  static $_TSPEC;


  public function __construct() {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        );
    }
  }

  public function getName() {
    return 'Calculator_ping_result';
  }

  public function read($input)
  {
    return $this->_read('Calculator_ping_result', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Calculator_ping_result', self::$_TSPEC, $output);
  }
}

class Calculator_add_args extends \TBase {
  static $_TSPEC;

  public $num1 = null;
  public $num2 = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'num1',
          'name' => 'i32',
          'type' => \TType::I32,
          ),
        2 => array(
          'var' => 'num2',
          'name' => 'i32',
          'type' => \TType::I32,
          ),
        );
    }
    if (is_array($vals)) {
      parent::__construct(self::$_TSPEC, $vals);
    }
  }

  public function getName() {
    return 'Calculator_add_args';
  }

  public function read($input)
  {
    return $this->_read('Calculator_add_args', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Calculator_add_args', self::$_TSPEC, $output);
  }
}

class Calculator_add_result extends \TBase {
  static $_TSPEC;

  public $success = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        0 => array(
          'var' => 'success',
          'name' => 'i32',
          'type' => \TType::I32,
          ),
        );
    }
    if (is_array($vals)) {
      parent::__construct(self::$_TSPEC, $vals);
    }
  }

  public function getName() {
    return 'Calculator_add_result';
  }

  public function read($input)
  {
    return $this->_read('Calculator_add_result', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Calculator_add_result', self::$_TSPEC, $output);
  }
}

class Calculator_calculate_args extends \TBase {
  static $_TSPEC;

  public $logid = null;
  public $w = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'logid',
          'name' => 'i32',
          'type' => \TType::I32,
          ),
        2 => array(
          'var' => 'w',
          'name' => '\Thrift\tutorial\Work',
          'type' => \TType::STRUCT,
          'class' => '\Thrift\tutorial\Work',
          ),
        );
    }
    if (is_array($vals)) {
      parent::__construct(self::$_TSPEC, $vals);
    }
  }

  public function getName() {
    return 'Calculator_calculate_args';
  }

  public function read($input)
  {
    return $this->_read('Calculator_calculate_args', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Calculator_calculate_args', self::$_TSPEC, $output);
  }
}

class Calculator_calculate_result extends \TBase {
  static $_TSPEC;

  public $success = null;
  public $ouch = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        0 => array(
          'var' => 'success',
          'name' => 'i32',
          'type' => \TType::I32,
          ),
        1 => array(
          'var' => 'ouch',
          'name' => '\Thrift\tutorial\InvalidOperation',
          'type' => \TType::STRUCT,
          'class' => '\Thrift\tutorial\InvalidOperation',
          ),
        );
    }
    if (is_array($vals)) {
      parent::__construct(self::$_TSPEC, $vals);
    }
  }

  public function getName() {
    return 'Calculator_calculate_result';
  }

  public function read($input)
  {
    return $this->_read('Calculator_calculate_result', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Calculator_calculate_result', self::$_TSPEC, $output);
  }
}

class Calculator_zip_args extends \TBase {
  static $_TSPEC;


  public function __construct() {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        );
    }
  }

  public function getName() {
    return 'Calculator_zip_args';
  }

  public function read($input)
  {
    return $this->_read('Calculator_zip_args', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Calculator_zip_args', self::$_TSPEC, $output);
  }
}

class CalculatorProcessor extends \Thrift\SharedServiceProcessor {
  public function __construct($handler) {
    parent::__construct($handler);
  }

  public function process($input, $output) {
    $rseqid = 0;
    $fname = null;
    $mtype = 0;

    $input->readMessageBegin($fname, $mtype, $rseqid);
    $methodname = 'process_'.$fname;
    if (!method_exists($this, $methodname)) {
      $input->skip(\TType::STRUCT);
      $input->readMessageEnd();
      $x = new \TApplicationException('Function '.$fname.' not implemented.', \TApplicationException::UNKNOWN_METHOD);
      $output->writeMessageBegin($fname, \TMessageType::EXCEPTION, $rseqid);
      $x->write($output);
      $output->writeMessageEnd();
      $output->getTransport()->flush();
      return;
    }
    $this->$methodname($rseqid, $input, $output);
    return true;
  }

  protected function process_ping($seqid, $input, $output) {
    $args = new \Thrift\tutorial\Calculator_ping_args();
    $args->read($input);
    $input->readMessageEnd();
    $result = new \Thrift\tutorial\Calculator_ping_result();
    $this->handler_->ping();
    $bin_accel = ($output instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($output, 'ping', \TMessageType::REPLY, $result, $seqid, $output->isStrictWrite());
    }
    else
    {
      $output->writeMessageBegin('ping', \TMessageType::REPLY, $seqid);
      $result->write($output);
      $output->getTransport()->flush();
    }
  }
  protected function process_add($seqid, $input, $output) {
    $args = new \Thrift\tutorial\Calculator_add_args();
    $args->read($input);
    $input->readMessageEnd();
    $result = new \Thrift\tutorial\Calculator_add_result();
    $result->success = $this->handler_->add($args->num1, $args->num2);
    $bin_accel = ($output instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($output, 'add', \TMessageType::REPLY, $result, $seqid, $output->isStrictWrite());
    }
    else
    {
      $output->writeMessageBegin('add', \TMessageType::REPLY, $seqid);
      $result->write($output);
      $output->getTransport()->flush();
    }
  }
  protected function process_calculate($seqid, $input, $output) {
    $args = new \Thrift\tutorial\Calculator_calculate_args();
    $args->read($input);
    $input->readMessageEnd();
    $result = new \Thrift\tutorial\Calculator_calculate_result();
    try {
      $result->success = $this->handler_->calculate($args->logid, $args->w);
    } catch (\Thrift\tutorial\InvalidOperation $ouch) {
      $result->ouch = $ouch;
    }
    $bin_accel = ($output instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($output, 'calculate', \TMessageType::REPLY, $result, $seqid, $output->isStrictWrite());
    }
    else
    {
      $output->writeMessageBegin('calculate', \TMessageType::REPLY, $seqid);
      $result->write($output);
      $output->getTransport()->flush();
    }
  }
  protected function process_zip($seqid, $input, $output) {
    $args = new \Thrift\tutorial\Calculator_zip_args();
    $args->read($input);
    $input->readMessageEnd();
    $this->handler_->zip();
    return;
  }
}
?>
