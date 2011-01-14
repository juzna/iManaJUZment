<?php
/**
 * This file is part of the "iManaJUZment" - complex system for internet service providers
 *
 * Copyright (c) 2005 - 2011 Jan Dolecek (http://juzna.cz)
 *
 * iManaJUZment is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with iManaJUZment.  If not, see <http://www.gnu.org/licenses/gpl.txt>.
 *
 * @license http://www.gnu.org/licenses/gpl.txt
 */


/**
 * Wrapper for ssh shell returned by ssh2_shell, which implements ISocket interface
 */
class SSHShellSocket implements ISocket {
  /**
   * PHP's resource holding the shell connection
   */
  protected $res;

  protected $sshConnection;

  protected $readerCallback;

  /**
   * Initializes new SHH Shell
   * @param resource $ssh Resource given from ssh2_connect function
   * @param resource $res Shell resource got from ssh2_shell
   */
  function __construct($ssh, $res) {
    $this->sshConnection = $ssh;
    $this->res = $res;

    // Make it non-blocking
    stream_set_blocking($res, false);
  }

  /**
   * Finish connection
   * @return void
   */
  function end() {
    throw new \NotSupportedException;
  }

  /**
   * Get list of file-descriptors, which can be polled
   * @return array
   */
  function getFDList() {
    static $fd; // Kinda workaround cuz my patch to libssh2 doesn't work as I expected
    if(!isset($fd)) $fd = ssh2_get_stream($this->sshConnection);

    return array($fd);
  }

  /**
   * Is this socket readable
   * @return bool
   */
  function isReadable() {
    return true;
  }

  /**
   * Is this socket writable
   * @return bool
   */
  function isWritable() {
    return true;
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
   * Try to read data
   * @return void
   */
  function read() {
    $data = fread($this->res, 10240);
    if($data !== false) call_user_func($this->readerCallback, $data);
  }

  /**
   * Write data to socket
   * @param string $data
   * @return bool If successfully written
   */
  function write($data) {
    fwrite($this->res, $data);
  }

  /**
   * Waits for some data to be ready
   * @throws InvalidStateException
   * @param int $timeout
   * @return bool
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
}
