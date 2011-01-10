<?php


class BaseSocket implements ISocket {
  protected $socket;
  protected $readerCallback;


  public function __construct($socket) {
    $this->socket = $socket;
  }

  /**
   * Waits for some incoming data to be ready
   * @param float $timeout
   * @return void
   */
  function waitForData($timeout = 5) {
    $read   = $this->getFDList();
    $write  = NULL;
    $except = NULL;

    if(false === ($numChangedStreams = stream_select($read, $write, $except, $timeout))) {
      throw new \InvalidStateException('Something wrong with stream_select');
    }
    else return $numChangedStreams > 0;
  }

  /**
   * Finish connection
   * @return void
   */
  function end() {
    fclose($this->socket);
  }

  /**
   * Try to read new data from socket
   * @return void
   */
  function read() {
    $data = fread($this->res, 10240);
    if($data !== false) call_user_func($this->readerCallback, $data);
  }

  /**
   * Set callback for incoming data
   * @param callable $cb func(data)
   * @return void
   */
  function setReader($cb) {
    $this->readerCallback = $cb;
  }

  /**
   * Write data to socket
   * @param string $data
   * @return bool If successfully written
   */
  function write($data) {
    fwrite($this->socket, $data);
  }

  /**
   * Is this socket readable
   * @return bool
   */
  function isReadable() {
    return !feof($this->socket);
  }

  /**
   * Is this socket writable
   * @return bool
   */
  function isWritable() {
    return !feof($this->socket);
  }

  /**
   * Get list of file-descriptors, which can be polled
   * @return array of int
   */
  function getFDList() {
    return array($this->socket);
  }
}
