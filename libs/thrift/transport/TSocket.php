<?php
/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements. See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership. The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the
 * specific language governing permissions and limitations
 * under the License.
 *
 * @package thrift.transport
 */


/**
 * Sockets implementation of the TTransport interface.
 *
 * @package thrift.transport
 */
class TSocket extends TTransport {

  /**
   * Handle to PHP socket
   *
   * @var resource
   */
  protected $handle_ = null;

  /**
   * Remote hostname
   *
   * @var string
   */
  protected $host_ = 'localhost';

  /**
   * Remote port
   *
   * @var int
   */
  protected $port_ = '9090';

  /**
   * Send timeout in milliseconds
   *
   * @var int
   */
  private $sendTimeout_ = 100;

  /**
   * Recv timeout in milliseconds
   *
   * @var int
   */
  private $recvTimeout_ = 750;

  /**
   * Is send timeout set?
   *
   * @var bool
   */
  private $sendTimeoutSet_ = FALSE;

  /**
   * Persistent socket or plain?
   *
   * @var bool
   */
  private $persist_ = FALSE;

  /**
   * Debugging on?
   *
   * @var bool
   */
  protected $debug_ = FALSE;

  /**
   * Debug handler
   *
   * @var mixed
   */
  protected $debugHandler_ = null;

  /**
   * Socket constructor
   *
   * @param string $host         Remote hostname
   * @param int    $port         Remote port
   * @param bool   $persist      Whether to use a persistent socket
   * @param string $debugHandler Function to call for error logging
   */
  public function __construct($host='localhost',
                              $port=9090,
                              $persist=FALSE,
                              $debugHandler=null) {
    $this->host_ = $host;
    $this->port_ = $port;
    $this->persist_ = $persist;
    $this->debugHandler_ = $debugHandler ? $debugHandler : 'error_log';
  }
  
  /**
   * Sets the send timeout.
   *
   * @param int $timeout  Timeout in milliseconds.
   */
  public function setSendTimeout($timeout) {
    $this->sendTimeout_ = $timeout;
  }

  /**
   * Sets the receive timeout.
   *
   * @param int $timeout  Timeout in milliseconds.
   */
  public function setRecvTimeout($timeout) {
    $this->recvTimeout_ = $timeout;
  }

  /**
   * Sets debugging output on or off
   *
   * @param bool $debug
   */
  public function setDebug($debug) {
    $this->debug_ = $debug;
  }

  /**
   * Get the host that this socket is connected to
   *
   * @return string host
   */
  public function getHost() {
    return $this->host_;
  }

  /**
   * Get the remote port that this socket is connected to
   *
   * @return int port
   */
  public function getPort() {
    return $this->port_;
  }

  /**
   * Tests whether this is open
   *
   * @return bool true if the socket is open
   */
  public function isOpen() {
    return is_resource($this->handle_);
  }

  /**
   * Connects the socket.
   */
  public function open() {
    if($this->isOpen()) return false;

    if ($this->persist_) {
      $this->handle_ = @pfsockopen($this->host_,
                                   $this->port_,
                                   $errno,
                                   $errstr,
                                   $this->sendTimeout_/1000.0);
    } else {
      $this->handle_ = @fsockopen($this->host_,
                                  $this->port_,
                                  $errno,
                                  $errstr,
                                  $this->sendTimeout_/1000.0);
    }

    // Connect failed?
    if ($this->handle_ === FALSE) {
      $error = 'TSocket: Could not connect to '.$this->host_.':'.$this->port_.' ('.$errstr.' ['.$errno.'])';
      if ($this->debug_) {
        call_user_func($this->debugHandler_, $error);
      }
      throw new TException($error, $errno);
    }

    stream_set_timeout($this->handle_, 0, $this->sendTimeout_*1000);
    $this->sendTimeoutSet_ = TRUE;
  }

  /**
   * Closes the socket.
   */
  public function close() {
    if (!$this->persist_) {
      @fclose($this->handle_);
      $this->handle_ = null;
    }
  }

  /**
   * Uses stream get contents to do the reading
   *
   * @param int $len How many bytes
   * @return string Binary data
   */
  public function readAll($len) {
    if ($this->sendTimeoutSet_) {
      stream_set_timeout($this->handle_, 0, $this->recvTimeout_*1000);
      $this->sendTimeoutSet_ = FALSE;
    }
    // This call does not obey stream_set_timeout values!
    // $buf = @stream_get_contents($this->handle_, $len);

    $pre = null;
    while (TRUE) {
      $buf = @fread($this->handle_, $len);
      if ($buf === FALSE || $buf === '') {
        $md = stream_get_meta_data($this->handle_);
        if ($md['timed_out']) {
          throw new TTransportException('TSocket: timed out reading '.$len.' bytes from '.
                               $this->host_.':'.$this->port_, TTransportException::TIMED_OUT);
        } elseif ($md['eof']) {
          throw new TTransportException('TSocket: end of file while reading'.$len.' bytes from '.
                               $this->host_.':'.$this->port_, TTransportException::END_OF_FILE);
        } else {
          throw new TTransportException('TSocket: Could not read '.$len.' bytes from '.
                               $this->host_.':'.$this->port_, TTransportException::UNKNOWN);
        }
      } else if (($sz = strlen($buf)) < $len) {
        $md = stream_get_meta_data($this->handle_);
        if ($md['timed_out']) {
          throw new TTransportException('TSocket: timed out reading '.$len.' bytes from '.
                               $this->host_.':'.$this->port_, TTransportException::TIMED_OUT);
        } else {
          $pre .= $buf;
          $len -= $sz;
        }
      } else {
        return $pre.$buf;
      }
    }
  }

  /**
   * Read from the socket
   *
   * @param int $len How many bytes
   * @return string Binary data
   */
  public function read($len) {
    if ($this->sendTimeoutSet_) {
      stream_set_timeout($this->handle_, 0, $this->recvTimeout_*1000);
      $this->sendTimeoutSet_ = FALSE;
    }
    $data = @fread($this->handle_, $len);
    if ($data === FALSE || $data === '') {
      $md = stream_get_meta_data($this->handle_);
      if ($md['timed_out']) {
        throw new TException('TSocket: timed out reading '.$len.' bytes from '.
                             $this->host_.':'.$this->port_);
      } else {
        throw new TException('TSocket: Could not read '.$len.' bytes from '.
                             $this->host_.':'.$this->port_);
      }
    }
    return $data;
  }

  /**
   * Write to the socket.
   *
   * @param string $buf The data to write
   */
  public function write($buf) {
    if (!$this->sendTimeoutSet_) {
      stream_set_timeout($this->handle_, 0, $this->sendTimeout_*1000);
      $this->sendTimeoutSet_ = TRUE;
    }
    while (strlen($buf) > 0) {
      $got = @fwrite($this->handle_, $buf);
      if ($got === 0 || $got === FALSE) {
        $md = stream_get_meta_data($this->handle_);
        if ($md['timed_out']) {
          throw new TException('TSocket: timed out writing '.strlen($buf).' bytes from '.
                               $this->host_.':'.$this->port_);
        } else {
            throw new TException('TSocket: Could not write '.strlen($buf).' bytes '.
                                 $this->host_.':'.$this->port_);
        }
      }
      $buf = substr($buf, $got);
    }
  }

  /**
   * Flush output to the socket.
   */
  public function flush() {
    $ret = fflush($this->handle_);
    if ($ret === FALSE) {
      throw new TException('TSocket: Could not flush: '.
                           $this->host_.':'.$this->port_);
    }
  }
}

class TClientSocket extends TSocket {
  /**
  * Create TSocket with existing handle
  */
  public function __construct($handle) {
  	$this->handle_ = $handle;
  	$this->host_ = null;
  	$this->port_ = null;
  }
  
  public function open() {
  	return false;
  }
}


class TServerSocket extends TSocket {
  private $streamListening;
  private $streamList = array();
  private $clientList = array();
  
  /**
  * Start listening
  */
  public function listen() {
  	$this->streamList[] = $this->streamListening = stream_socket_server($this->host_);
  }
  
  public function isOpen() {
  	return is_resource($this->streamListening);
  }
  
  public function close() {
  	@fclose($this->streamListening);
  	$this->streamListening = null;
  }
  
  public function select($processor, $timeout = 30) {
	static $errors = 0;
	set_time_limit(60);
	$read = $this->streamList;
	$write = $except = array();
	
	$ret = stream_select($read, $write, $except, $timeout);
	if($ret === false) { // Error while select
		$errors++;
		echo "Select error num $errors\n";
		if($errors > 20) return new Exception('Error while stream_select');
		else return 0;
	}
	elseif($ret > 0) { // Some data available
		if($errors > 0) {
			echo "Clearing $errors errors\n";
			$errors = 0; // Clear errors
		}
		
		// Process all available sockets
		foreach($read as $stream) {
			// Listening socket has changes -> accept new connections
			if($stream == $this->streamListening) {
				$newStream = stream_socket_accept($this->streamListening);
				$this->streamList[] = $newStream;
				
				// Add new client
				$sock = new TClientSocket($newStream);
				$transport = new TBufferedTransport($sock);
				$proto = new TBinaryProtocol($transport, true, true);
				$this->clientList["$newStream"] = $proto;
				
				// Open transport
				$transport->open();
			}
			
			// It's our client
			elseif($client = $this->clientList["$stream"]) {
				try {
					$processor->process($client, $client);
				}
				catch(TTransportException $e) {
					$this->disconnect($stream);
				}
				catch(TException $e) {
					echo "Error: " . $e->getMessage() . "\n";
					$this->disconnect($stream);
				}
			}
		}
	}
	
	return $ret;
  }
  
  public function disconnect($stream) {
	// Close socket
	stream_socket_shutdown($stream, STREAM_SHUT_RDWR);

	$key = array_search($this->clientList["$stream"], $this->clientList);
	unset($this->clientList[$key]);
	
	$key = array_search($stream, $this->streamList);
	unset($this->streamList[$key]);
  }
}

?>
