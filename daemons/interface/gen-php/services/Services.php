<?php
namespace Thrift\APos;
/**
 * Autogenerated by Thrift
 *
 * DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
 */
include_once $GLOBALS['THRIFT_ROOT'].'/Thrift.php';

include_once $GLOBALS['THRIFT_ROOT'].'/packages/services/services_types.php';

interface ServicesIf {
  public function checkService($serviceName);
  public function activateService($serviceName);
  public function deactivateService($serviceName);
  public function getAvailableServices();
  public function isSupported($serviceName);
  public function checkAllServices();
}

class ServicesClient implements \Thrift\APos\ServicesIf {
  protected $input_ = null;
  protected $output_ = null;

  protected $seqid_ = 0;

  public function __construct($input, $output=null) {
    $this->input_ = $input;
    $this->output_ = $output ? $output : $input;
  }

  public function checkService($serviceName)
  {
    $this->send_checkService($serviceName);
    return $this->recv_checkService();
  }

  public function send_checkService($serviceName)
  {
    $args = new \Thrift\APos\Services_checkService_args();
    $args->serviceName = $serviceName;
    $bin_accel = ($this->output_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($this->output_, 'checkService', \TMessageType::CALL, $args, $this->seqid_, $this->output_->isStrictWrite());
    }
    else
    {
      $this->output_->writeMessageBegin('checkService', \TMessageType::CALL, $this->seqid_);
      $args->write($this->output_);
      $this->output_->writeMessageEnd();
      $this->output_->getTransport()->flush();
    }
  }

  public function recv_checkService()
  {
    $bin_accel = ($this->input_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_read_binary');
    if ($bin_accel) $result = thrift_protocol_read_binary($this->input_, '\Thrift\APos\Services_checkService_result', $this->input_->isStrictRead());
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
      $result = new \Thrift\APos\Services_checkService_result();
      $result->read($this->input_);
      $this->input_->readMessageEnd();
    }
    if ($result->success !== null) {
      return $result->success;
    }
    throw new \Exception("checkService failed: unknown result");
  }

  public function activateService($serviceName)
  {
    $this->send_activateService($serviceName);
  }

  public function send_activateService($serviceName)
  {
    $args = new \Thrift\APos\Services_activateService_args();
    $args->serviceName = $serviceName;
    $bin_accel = ($this->output_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($this->output_, 'activateService', \TMessageType::CALL, $args, $this->seqid_, $this->output_->isStrictWrite());
    }
    else
    {
      $this->output_->writeMessageBegin('activateService', \TMessageType::CALL, $this->seqid_);
      $args->write($this->output_);
      $this->output_->writeMessageEnd();
      $this->output_->getTransport()->flush();
    }
  }
  public function deactivateService($serviceName)
  {
    $this->send_deactivateService($serviceName);
  }

  public function send_deactivateService($serviceName)
  {
    $args = new \Thrift\APos\Services_deactivateService_args();
    $args->serviceName = $serviceName;
    $bin_accel = ($this->output_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($this->output_, 'deactivateService', \TMessageType::CALL, $args, $this->seqid_, $this->output_->isStrictWrite());
    }
    else
    {
      $this->output_->writeMessageBegin('deactivateService', \TMessageType::CALL, $this->seqid_);
      $args->write($this->output_);
      $this->output_->writeMessageEnd();
      $this->output_->getTransport()->flush();
    }
  }
  public function getAvailableServices()
  {
    $this->send_getAvailableServices();
    return $this->recv_getAvailableServices();
  }

  public function send_getAvailableServices()
  {
    $args = new \Thrift\APos\Services_getAvailableServices_args();
    $bin_accel = ($this->output_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($this->output_, 'getAvailableServices', \TMessageType::CALL, $args, $this->seqid_, $this->output_->isStrictWrite());
    }
    else
    {
      $this->output_->writeMessageBegin('getAvailableServices', \TMessageType::CALL, $this->seqid_);
      $args->write($this->output_);
      $this->output_->writeMessageEnd();
      $this->output_->getTransport()->flush();
    }
  }

  public function recv_getAvailableServices()
  {
    $bin_accel = ($this->input_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_read_binary');
    if ($bin_accel) $result = thrift_protocol_read_binary($this->input_, '\Thrift\APos\Services_getAvailableServices_result', $this->input_->isStrictRead());
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
      $result = new \Thrift\APos\Services_getAvailableServices_result();
      $result->read($this->input_);
      $this->input_->readMessageEnd();
    }
    if ($result->success !== null) {
      return $result->success;
    }
    throw new \Exception("getAvailableServices failed: unknown result");
  }

  public function isSupported($serviceName)
  {
    $this->send_isSupported($serviceName);
    return $this->recv_isSupported();
  }

  public function send_isSupported($serviceName)
  {
    $args = new \Thrift\APos\Services_isSupported_args();
    $args->serviceName = $serviceName;
    $bin_accel = ($this->output_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($this->output_, 'isSupported', \TMessageType::CALL, $args, $this->seqid_, $this->output_->isStrictWrite());
    }
    else
    {
      $this->output_->writeMessageBegin('isSupported', \TMessageType::CALL, $this->seqid_);
      $args->write($this->output_);
      $this->output_->writeMessageEnd();
      $this->output_->getTransport()->flush();
    }
  }

  public function recv_isSupported()
  {
    $bin_accel = ($this->input_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_read_binary');
    if ($bin_accel) $result = thrift_protocol_read_binary($this->input_, '\Thrift\APos\Services_isSupported_result', $this->input_->isStrictRead());
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
      $result = new \Thrift\APos\Services_isSupported_result();
      $result->read($this->input_);
      $this->input_->readMessageEnd();
    }
    if ($result->success !== null) {
      return $result->success;
    }
    throw new \Exception("isSupported failed: unknown result");
  }

  public function checkAllServices()
  {
    $this->send_checkAllServices();
    return $this->recv_checkAllServices();
  }

  public function send_checkAllServices()
  {
    $args = new \Thrift\APos\Services_checkAllServices_args();
    $bin_accel = ($this->output_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($this->output_, 'checkAllServices', \TMessageType::CALL, $args, $this->seqid_, $this->output_->isStrictWrite());
    }
    else
    {
      $this->output_->writeMessageBegin('checkAllServices', \TMessageType::CALL, $this->seqid_);
      $args->write($this->output_);
      $this->output_->writeMessageEnd();
      $this->output_->getTransport()->flush();
    }
  }

  public function recv_checkAllServices()
  {
    $bin_accel = ($this->input_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_read_binary');
    if ($bin_accel) $result = thrift_protocol_read_binary($this->input_, '\Thrift\APos\Services_checkAllServices_result', $this->input_->isStrictRead());
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
      $result = new \Thrift\APos\Services_checkAllServices_result();
      $result->read($this->input_);
      $this->input_->readMessageEnd();
    }
    if ($result->success !== null) {
      return $result->success;
    }
    throw new \Exception("checkAllServices failed: unknown result");
  }

}

// HELPER FUNCTIONS AND STRUCTURES

class Services_checkService_args extends \TBase {
  static $_TSPEC;

  public $serviceName = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'serviceName',
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
    return 'Services_checkService_args';
  }

  public function read($input)
  {
    return $this->_read('Services_checkService_args', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Services_checkService_args', self::$_TSPEC, $output);
  }
}

class Services_checkService_result extends \TBase {
  static $_TSPEC;

  public $success = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        0 => array(
          'var' => 'success',
          'name' => '\Thrift\APos\ServiceState',
          'type' => \TType::STRUCT,
          'class' => '\Thrift\APos\ServiceState',
          ),
        );
    }
    if (is_array($vals)) {
      parent::__construct(self::$_TSPEC, $vals);
    }
  }

  public function getName() {
    return 'Services_checkService_result';
  }

  public function read($input)
  {
    return $this->_read('Services_checkService_result', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Services_checkService_result', self::$_TSPEC, $output);
  }
}

class Services_activateService_args extends \TBase {
  static $_TSPEC;

  public $serviceName = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'serviceName',
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
    return 'Services_activateService_args';
  }

  public function read($input)
  {
    return $this->_read('Services_activateService_args', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Services_activateService_args', self::$_TSPEC, $output);
  }
}

class Services_deactivateService_args extends \TBase {
  static $_TSPEC;

  public $serviceName = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'serviceName',
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
    return 'Services_deactivateService_args';
  }

  public function read($input)
  {
    return $this->_read('Services_deactivateService_args', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Services_deactivateService_args', self::$_TSPEC, $output);
  }
}

class Services_getAvailableServices_args extends \TBase {
  static $_TSPEC;


  public function __construct() {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        );
    }
  }

  public function getName() {
    return 'Services_getAvailableServices_args';
  }

  public function read($input)
  {
    return $this->_read('Services_getAvailableServices_args', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Services_getAvailableServices_args', self::$_TSPEC, $output);
  }
}

class Services_getAvailableServices_result extends \TBase {
  static $_TSPEC;

  public $success = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        0 => array(
          'var' => 'success',
          'name' => '',
          'type' => \TType::LST,
          'etype' => \TType::STRUCT,
          'elem' => array(
            'name' => '\Thrift\APos\ServiceDescriptor',
            'type' => \TType::STRUCT,
            'class' => '\Thrift\APos\ServiceDescriptor',
            ),
          ),
        );
    }
    if (is_array($vals)) {
      parent::__construct(self::$_TSPEC, $vals);
    }
  }

  public function getName() {
    return 'Services_getAvailableServices_result';
  }

  public function read($input)
  {
    return $this->_read('Services_getAvailableServices_result', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Services_getAvailableServices_result', self::$_TSPEC, $output);
  }
}

class Services_isSupported_args extends \TBase {
  static $_TSPEC;

  public $serviceName = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'serviceName',
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
    return 'Services_isSupported_args';
  }

  public function read($input)
  {
    return $this->_read('Services_isSupported_args', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Services_isSupported_args', self::$_TSPEC, $output);
  }
}

class Services_isSupported_result extends \TBase {
  static $_TSPEC;

  public $success = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        0 => array(
          'var' => 'success',
          'name' => 'bool',
          'type' => \TType::BOOL,
          ),
        );
    }
    if (is_array($vals)) {
      parent::__construct(self::$_TSPEC, $vals);
    }
  }

  public function getName() {
    return 'Services_isSupported_result';
  }

  public function read($input)
  {
    return $this->_read('Services_isSupported_result', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Services_isSupported_result', self::$_TSPEC, $output);
  }
}

class Services_checkAllServices_args extends \TBase {
  static $_TSPEC;


  public function __construct() {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        );
    }
  }

  public function getName() {
    return 'Services_checkAllServices_args';
  }

  public function read($input)
  {
    return $this->_read('Services_checkAllServices_args', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Services_checkAllServices_args', self::$_TSPEC, $output);
  }
}

class Services_checkAllServices_result extends \TBase {
  static $_TSPEC;

  public $success = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        0 => array(
          'var' => 'success',
          'name' => '',
          'type' => \TType::MAP,
          'ktype' => \TType::STRING,
          'vtype' => \TType::STRUCT,
          'key' => array(
            'name' => 'string',
            'type' => \TType::STRING,
          ),
          'val' => array(
            'name' => '\Thrift\APos\ServiceState',
            'type' => \TType::STRUCT,
            'class' => '\Thrift\APos\ServiceState',
            ),
          ),
        );
    }
    if (is_array($vals)) {
      parent::__construct(self::$_TSPEC, $vals);
    }
  }

  public function getName() {
    return 'Services_checkAllServices_result';
  }

  public function read($input)
  {
    return $this->_read('Services_checkAllServices_result', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Services_checkAllServices_result', self::$_TSPEC, $output);
  }
}

class ServicesProcessor {
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

  protected function process_checkService($seqid, $input, $output) {
    $args = new \Thrift\APos\Services_checkService_args();
    $args->read($input);
    $input->readMessageEnd();
    $result = new \Thrift\APos\Services_checkService_result();
    $result->success = $this->handler_->checkService($args->serviceName);
    $bin_accel = ($output instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($output, 'checkService', \TMessageType::REPLY, $result, $seqid, $output->isStrictWrite());
    }
    else
    {
      $output->writeMessageBegin('checkService', \TMessageType::REPLY, $seqid);
      $result->write($output);
      $output->getTransport()->flush();
    }
  }
  protected function process_activateService($seqid, $input, $output) {
    $args = new \Thrift\APos\Services_activateService_args();
    $args->read($input);
    $input->readMessageEnd();
    $this->handler_->activateService($args->serviceName);
    return;
  }
  protected function process_deactivateService($seqid, $input, $output) {
    $args = new \Thrift\APos\Services_deactivateService_args();
    $args->read($input);
    $input->readMessageEnd();
    $this->handler_->deactivateService($args->serviceName);
    return;
  }
  protected function process_getAvailableServices($seqid, $input, $output) {
    $args = new \Thrift\APos\Services_getAvailableServices_args();
    $args->read($input);
    $input->readMessageEnd();
    $result = new \Thrift\APos\Services_getAvailableServices_result();
    $result->success = $this->handler_->getAvailableServices();
    $bin_accel = ($output instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($output, 'getAvailableServices', \TMessageType::REPLY, $result, $seqid, $output->isStrictWrite());
    }
    else
    {
      $output->writeMessageBegin('getAvailableServices', \TMessageType::REPLY, $seqid);
      $result->write($output);
      $output->getTransport()->flush();
    }
  }
  protected function process_isSupported($seqid, $input, $output) {
    $args = new \Thrift\APos\Services_isSupported_args();
    $args->read($input);
    $input->readMessageEnd();
    $result = new \Thrift\APos\Services_isSupported_result();
    $result->success = $this->handler_->isSupported($args->serviceName);
    $bin_accel = ($output instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($output, 'isSupported', \TMessageType::REPLY, $result, $seqid, $output->isStrictWrite());
    }
    else
    {
      $output->writeMessageBegin('isSupported', \TMessageType::REPLY, $seqid);
      $result->write($output);
      $output->getTransport()->flush();
    }
  }
  protected function process_checkAllServices($seqid, $input, $output) {
    $args = new \Thrift\APos\Services_checkAllServices_args();
    $args->read($input);
    $input->readMessageEnd();
    $result = new \Thrift\APos\Services_checkAllServices_result();
    $result->success = $this->handler_->checkAllServices();
    $bin_accel = ($output instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($output, 'checkAllServices', \TMessageType::REPLY, $result, $seqid, $output->isStrictWrite());
    }
    else
    {
      $output->writeMessageBegin('checkAllServices', \TMessageType::REPLY, $seqid);
      $result->write($output);
      $output->getTransport()->flush();
    }
  }
}
?>
