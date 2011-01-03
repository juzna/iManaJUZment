<?php
namespace Thrift;
/**
 * Autogenerated by Thrift
 *
 * DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
 */
include_once $GLOBALS['THRIFT_ROOT'].'/Thrift.php';

include_once $GLOBALS['THRIFT_ROOT'].'/packages/ban/ban_types.php';

interface BlockingIf {
  public function block($user, $type);
  public function unblock($user);
  public function ensureBanned($user, $type);
  public function isBanned($user);
  public function getBanList();
  public function blockMultiple($banList);
}

class BlockingClient implements \Thrift\BlockingIf {
  protected $input_ = null;
  protected $output_ = null;

  protected $seqid_ = 0;

  public function __construct($input, $output=null) {
    $this->input_ = $input;
    $this->output_ = $output ? $output : $input;
  }

  public function block($user, $type)
  {
    $this->send_block($user, $type);
  }

  public function send_block($user, $type)
  {
    $args = new \Thrift\Blocking_block_args();
    $args->user = $user;
    $args->type = $type;
    $bin_accel = ($this->output_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($this->output_, 'block', \TMessageType::CALL, $args, $this->seqid_, $this->output_->isStrictWrite());
    }
    else
    {
      $this->output_->writeMessageBegin('block', \TMessageType::CALL, $this->seqid_);
      $args->write($this->output_);
      $this->output_->writeMessageEnd();
      $this->output_->getTransport()->flush();
    }
  }
  public function unblock($user)
  {
    $this->send_unblock($user);
  }

  public function send_unblock($user)
  {
    $args = new \Thrift\Blocking_unblock_args();
    $args->user = $user;
    $bin_accel = ($this->output_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($this->output_, 'unblock', \TMessageType::CALL, $args, $this->seqid_, $this->output_->isStrictWrite());
    }
    else
    {
      $this->output_->writeMessageBegin('unblock', \TMessageType::CALL, $this->seqid_);
      $args->write($this->output_);
      $this->output_->writeMessageEnd();
      $this->output_->getTransport()->flush();
    }
  }
  public function ensureBanned($user, $type)
  {
    $this->send_ensureBanned($user, $type);
  }

  public function send_ensureBanned($user, $type)
  {
    $args = new \Thrift\Blocking_ensureBanned_args();
    $args->user = $user;
    $args->type = $type;
    $bin_accel = ($this->output_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($this->output_, 'ensureBanned', \TMessageType::CALL, $args, $this->seqid_, $this->output_->isStrictWrite());
    }
    else
    {
      $this->output_->writeMessageBegin('ensureBanned', \TMessageType::CALL, $this->seqid_);
      $args->write($this->output_);
      $this->output_->writeMessageEnd();
      $this->output_->getTransport()->flush();
    }
  }
  public function isBanned($user)
  {
    $this->send_isBanned($user);
    return $this->recv_isBanned();
  }

  public function send_isBanned($user)
  {
    $args = new \Thrift\Blocking_isBanned_args();
    $args->user = $user;
    $bin_accel = ($this->output_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($this->output_, 'isBanned', \TMessageType::CALL, $args, $this->seqid_, $this->output_->isStrictWrite());
    }
    else
    {
      $this->output_->writeMessageBegin('isBanned', \TMessageType::CALL, $this->seqid_);
      $args->write($this->output_);
      $this->output_->writeMessageEnd();
      $this->output_->getTransport()->flush();
    }
  }

  public function recv_isBanned()
  {
    $bin_accel = ($this->input_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_read_binary');
    if ($bin_accel) $result = thrift_protocol_read_binary($this->input_, '\Thrift\Blocking_isBanned_result', $this->input_->isStrictRead());
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
      $result = new \Thrift\Blocking_isBanned_result();
      $result->read($this->input_);
      $this->input_->readMessageEnd();
    }
    if ($result->success !== null) {
      return $result->success;
    }
    throw new \Exception("isBanned failed: unknown result");
  }

  public function getBanList()
  {
    $this->send_getBanList();
    return $this->recv_getBanList();
  }

  public function send_getBanList()
  {
    $args = new \Thrift\Blocking_getBanList_args();
    $bin_accel = ($this->output_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($this->output_, 'getBanList', \TMessageType::CALL, $args, $this->seqid_, $this->output_->isStrictWrite());
    }
    else
    {
      $this->output_->writeMessageBegin('getBanList', \TMessageType::CALL, $this->seqid_);
      $args->write($this->output_);
      $this->output_->writeMessageEnd();
      $this->output_->getTransport()->flush();
    }
  }

  public function recv_getBanList()
  {
    $bin_accel = ($this->input_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_read_binary');
    if ($bin_accel) $result = thrift_protocol_read_binary($this->input_, '\Thrift\Blocking_getBanList_result', $this->input_->isStrictRead());
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
      $result = new \Thrift\Blocking_getBanList_result();
      $result->read($this->input_);
      $this->input_->readMessageEnd();
    }
    if ($result->success !== null) {
      return $result->success;
    }
    throw new \Exception("getBanList failed: unknown result");
  }

  public function blockMultiple($banList)
  {
    $this->send_blockMultiple($banList);
  }

  public function send_blockMultiple($banList)
  {
    $args = new \Thrift\Blocking_blockMultiple_args();
    $args->banList = $banList;
    $bin_accel = ($this->output_ instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($this->output_, 'blockMultiple', \TMessageType::CALL, $args, $this->seqid_, $this->output_->isStrictWrite());
    }
    else
    {
      $this->output_->writeMessageBegin('blockMultiple', \TMessageType::CALL, $this->seqid_);
      $args->write($this->output_);
      $this->output_->writeMessageEnd();
      $this->output_->getTransport()->flush();
    }
  }
}

// HELPER FUNCTIONS AND STRUCTURES

class Blocking_block_args extends \TBase {
  static $_TSPEC;

  public $user = null;
  public $type = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'user',
          'name' => '\Thrift\BlockUser',
          'type' => \TType::STRUCT,
          'class' => '\Thrift\BlockUser',
          ),
        2 => array(
          'var' => 'type',
          'name' => '\Thrift\BlockType',
          'type' => \TType::ENUM,
          'enum' => '\Thrift\E_BlockType',
          ),
        );
    }
    if (is_array($vals)) {
      parent::__construct(self::$_TSPEC, $vals);
    }
  }

  public function getName() {
    return 'Blocking_block_args';
  }

  public function read($input)
  {
    return $this->_read('Blocking_block_args', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Blocking_block_args', self::$_TSPEC, $output);
  }
}

class Blocking_unblock_args extends \TBase {
  static $_TSPEC;

  public $user = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'user',
          'name' => '\Thrift\BlockUser',
          'type' => \TType::STRUCT,
          'class' => '\Thrift\BlockUser',
          ),
        );
    }
    if (is_array($vals)) {
      parent::__construct(self::$_TSPEC, $vals);
    }
  }

  public function getName() {
    return 'Blocking_unblock_args';
  }

  public function read($input)
  {
    return $this->_read('Blocking_unblock_args', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Blocking_unblock_args', self::$_TSPEC, $output);
  }
}

class Blocking_ensureBanned_args extends \TBase {
  static $_TSPEC;

  public $user = null;
  public $type = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'user',
          'name' => '\Thrift\BlockUser',
          'type' => \TType::STRUCT,
          'class' => '\Thrift\BlockUser',
          ),
        2 => array(
          'var' => 'type',
          'name' => '\Thrift\BlockType',
          'type' => \TType::ENUM,
          'enum' => '\Thrift\E_BlockType',
          ),
        );
    }
    if (is_array($vals)) {
      parent::__construct(self::$_TSPEC, $vals);
    }
  }

  public function getName() {
    return 'Blocking_ensureBanned_args';
  }

  public function read($input)
  {
    return $this->_read('Blocking_ensureBanned_args', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Blocking_ensureBanned_args', self::$_TSPEC, $output);
  }
}

class Blocking_isBanned_args extends \TBase {
  static $_TSPEC;

  public $user = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'user',
          'name' => '\Thrift\BlockUser',
          'type' => \TType::STRUCT,
          'class' => '\Thrift\BlockUser',
          ),
        );
    }
    if (is_array($vals)) {
      parent::__construct(self::$_TSPEC, $vals);
    }
  }

  public function getName() {
    return 'Blocking_isBanned_args';
  }

  public function read($input)
  {
    return $this->_read('Blocking_isBanned_args', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Blocking_isBanned_args', self::$_TSPEC, $output);
  }
}

class Blocking_isBanned_result extends \TBase {
  static $_TSPEC;

  public $success = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        0 => array(
          'var' => 'success',
          'name' => '\Thrift\BlockType',
          'type' => \TType::ENUM,
          'enum' => '\Thrift\E_BlockType',
          ),
        );
    }
    if (is_array($vals)) {
      parent::__construct(self::$_TSPEC, $vals);
    }
  }

  public function getName() {
    return 'Blocking_isBanned_result';
  }

  public function read($input)
  {
    return $this->_read('Blocking_isBanned_result', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Blocking_isBanned_result', self::$_TSPEC, $output);
  }
}

class Blocking_getBanList_args extends \TBase {
  static $_TSPEC;


  public function __construct() {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        );
    }
  }

  public function getName() {
    return 'Blocking_getBanList_args';
  }

  public function read($input)
  {
    return $this->_read('Blocking_getBanList_args', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Blocking_getBanList_args', self::$_TSPEC, $output);
  }
}

class Blocking_getBanList_result extends \TBase {
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
            'name' => '\Thrift\BanEntry',
            'type' => \TType::STRUCT,
            'class' => '\Thrift\BanEntry',
            ),
          ),
        );
    }
    if (is_array($vals)) {
      parent::__construct(self::$_TSPEC, $vals);
    }
  }

  public function getName() {
    return 'Blocking_getBanList_result';
  }

  public function read($input)
  {
    return $this->_read('Blocking_getBanList_result', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Blocking_getBanList_result', self::$_TSPEC, $output);
  }
}

class Blocking_blockMultiple_args extends \TBase {
  static $_TSPEC;

  public $banList = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'banList',
          'name' => '',
          'type' => \TType::LST,
          'etype' => \TType::STRUCT,
          'elem' => array(
            'name' => '\Thrift\BanEntry',
            'type' => \TType::STRUCT,
            'class' => '\Thrift\BanEntry',
            ),
          ),
        );
    }
    if (is_array($vals)) {
      parent::__construct(self::$_TSPEC, $vals);
    }
  }

  public function getName() {
    return 'Blocking_blockMultiple_args';
  }

  public function read($input)
  {
    return $this->_read('Blocking_blockMultiple_args', self::$_TSPEC, $input);
  }
  public function write($output) {
    return $this->_write('Blocking_blockMultiple_args', self::$_TSPEC, $output);
  }
}

class BlockingProcessor {
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

  protected function process_block($seqid, $input, $output) {
    $args = new \Thrift\Blocking_block_args();
    $args->read($input);
    $input->readMessageEnd();
    $this->handler_->block($args->user, $args->type);
    return;
  }
  protected function process_unblock($seqid, $input, $output) {
    $args = new \Thrift\Blocking_unblock_args();
    $args->read($input);
    $input->readMessageEnd();
    $this->handler_->unblock($args->user);
    return;
  }
  protected function process_ensureBanned($seqid, $input, $output) {
    $args = new \Thrift\Blocking_ensureBanned_args();
    $args->read($input);
    $input->readMessageEnd();
    $this->handler_->ensureBanned($args->user, $args->type);
    return;
  }
  protected function process_isBanned($seqid, $input, $output) {
    $args = new \Thrift\Blocking_isBanned_args();
    $args->read($input);
    $input->readMessageEnd();
    $result = new \Thrift\Blocking_isBanned_result();
    $result->success = $this->handler_->isBanned($args->user);
    $bin_accel = ($output instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($output, 'isBanned', \TMessageType::REPLY, $result, $seqid, $output->isStrictWrite());
    }
    else
    {
      $output->writeMessageBegin('isBanned', \TMessageType::REPLY, $seqid);
      $result->write($output);
      $output->getTransport()->flush();
    }
  }
  protected function process_getBanList($seqid, $input, $output) {
    $args = new \Thrift\Blocking_getBanList_args();
    $args->read($input);
    $input->readMessageEnd();
    $result = new \Thrift\Blocking_getBanList_result();
    $result->success = $this->handler_->getBanList();
    $bin_accel = ($output instanceof \TProtocol::$TBINARYPROTOCOLACCELERATED) && function_exists('thrift_protocol_write_binary');
    if ($bin_accel)
    {
      thrift_protocol_write_binary($output, 'getBanList', \TMessageType::REPLY, $result, $seqid, $output->isStrictWrite());
    }
    else
    {
      $output->writeMessageBegin('getBanList', \TMessageType::REPLY, $seqid);
      $result->write($output);
      $output->getTransport()->flush();
    }
  }
  protected function process_blockMultiple($seqid, $input, $output) {
    $args = new \Thrift\Blocking_blockMultiple_args();
    $args->read($input);
    $input->readMessageEnd();
    $this->handler_->blockMultiple($args->banList);
    return;
  }
}
?>
