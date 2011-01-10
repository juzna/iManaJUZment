<?php

interface ISocket {
  /**
   * Get list of file-descriptors, which can be polled
   * @return array of int
   */
  function getFDList();

  /**
   * Is this socket writable
   * @return bool
   */
  function isWritable();
  
  /**
   * Is this socket readable
   * @return bool
   */
  function isReadable();

  /**
   * Write data to socket
   * @param string $data
   * @return bool If successfully written
   */
  function write($data);

  /**
   * Set callback for incoming data
   * @param callable $cb func(data)
   * @return void
   */
  function setReader($cb);

  /**
   * Try to read new data from socket
   * @return void
   */
  function read();

  /**
   * Finish connection
   * @return void
   */
  function end();

  /**
   * Waits for some incoming data to be ready
   * @param float $timeout
   * @return void
   */
  function waitForData($timeout);
}


