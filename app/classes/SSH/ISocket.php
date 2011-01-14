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


