<?php
namespace Thrift;
/**
 * Autogenerated by Thrift
 *
 * DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
 */
include_once $GLOBALS['THRIFT_ROOT'].'/Thrift.php';

include_once $GLOBALS['THRIFT_ROOT'].'/packages/shared/shared_types.php';

interface SharedServiceIf {
  public function getStruct($key);
}

class SharedServiceClient implements \Thrift\SharedServiceIf {
  protected $input_ = null;
  protected $output_ = null;

  protected $seqid_ = 0;

  public function __construct($input, $output=null) {
    $this->input_ = $input;
    $this->output_ = $output ? $output : $input;
  }

  public function getStruct($key)
  {
    $this->send_getStruct($key);
    return $this->recv_getStruct();
  }

  public function send_getStruct($key)
  {
    $args = new \Thrift\SharedService_getStruct_args();
    $args->key = $key;
    $bin_accel = ($this->output_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($this->output_, 'getStruct', \TMessageType::CALL, $args, $this->seqid_, $this->output_->isStrictWrite());
    }
    else
    {
      $this->output_->writeMessageBegin('getStruct', \TMessageType::CALL, $this->seqid_);
      $args->write($this->output_);
      $this->output_->writeMessageEnd();
      $this->output_->getTransport()->flush();
    }
  }

  public function recv_getStruct()
  {
    $bin_accel = ($this->input_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_read_binary');
    if ($bin_accel) $result = thrift_protocol_read_binary($this->input_, '\Thrift\SharedService_getStruct_result', $this->input_->isStrictRead());
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
      $result = new \Thrift\SharedService_getStruct_result();
      $result->read($this->input_);
      $this->input_->readMessageEnd();
    }
    if ($result->success !== null) {
      return $result->success;
    }
    throw new \Exception("getStruct failed: unknown result");
  }

}

// HELPER FUNCTIONS AND STRUCTURES

class SharedService_getStruct_args extends \TBase {
  static $_TSPEC;

  public $key = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'key',
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
    return 'SharedService_getStruct_args';
  }

  public function read($input)
  {
    return $this->_read('SharedService_getStruct_args', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('SharedService_getStruct_args', self::$_TSPEC, $output);
  }
}

class SharedService_getStruct_result extends \TBase {
  static $_TSPEC;

  public $success = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        0 => array(
          'var' => 'success',
          'name' => '\Thrift\SharedStruct',
          'type' => \TType::STRUCT,
          'class' => '\Thrift\SharedStruct',
          ),
        );
    }
    if (is_array($vals)) {
      parent::__construct(self::$_TSPEC, $vals);
    }
  }

  public function getName() {
    return 'SharedService_getStruct_result';
  }

  public function read($input)
  {
    return $this->_read('SharedService_getStruct_result', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('SharedService_getStruct_result', self::$_TSPEC, $output);
  }
}

class SharedServiceProcessor {
  protected $handler_ = null;
  public function __construct($handler) {
    $this->handler_ = $handler;
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

  protected function process_getStruct($seqid, $input, $output) {
    $args = new \Thrift\SharedService_getStruct_args();
    $args->read($input);
    $input->readMessageEnd();
    $result = new \Thrift\SharedService_getStruct_result();
    $result->success = $this->handler_->getStruct($args->key);
    $bin_accel = ($output instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($output, 'getStruct', \TMessageType::REPLY, $result, $seqid, $output->isStrictWrite());
    }
    else
    {
      $output->writeMessageBegin('getStruct', \TMessageType::REPLY, $seqid);
      $result->write($output);
      $output->getTransport()->flush();
    }
  }
}
?>
