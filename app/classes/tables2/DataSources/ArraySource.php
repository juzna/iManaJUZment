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

namespace Juz\Tables\DataSource;

class ArraySource extends \Nette\Object implements \Juz\Tables\IDataSource {
  protected $list = null;
  protected $keys = null;
  protected $position = null;

  public function __construct(array $list) {
    $this->list = $list;
    $this->keys = array_keys($list);
    $this->position = 0;
  }

  /**
   * (PHP 5 &gt;= 5.1.0)<br/>
   * Count elements of an object
   * @link http://php.net/manual/en/countable.count.php
   * @return int The custom count as an integer.
   * </p>
   * <p>
   * The return value is cast to an integer.
   */
  public function count() {
    return count($this->keys);
  }

  /**
   * (PHP 5 &gt;= 5.1.0)<br/>
   * Rewind the Iterator to the first element
   * @link http://php.net/manual/en/iterator.rewind.php
   * @return void Any returned value is ignored.
   */
  public function rewind() {
    $this->position = 0;
  }

  /**
   * (PHP 5 &gt;= 5.1.0)<br/>
   * Checks if current position is valid
   * @link http://php.net/manual/en/iterator.valid.php
   * @return boolean The return value will be casted to boolean and then evaluated.
   * Returns true on success or false on failure.
   */
  public function valid() {
    return isset($this->keys[$this->position]);
  }

  /**
   * (PHP 5 &gt;= 5.1.0)<br/>
   * Return the key of the current element
   * @link http://php.net/manual/en/iterator.key.php
   * @return scalar scalar on success, integer
   * 0 on failure.
   */
  public function key() {
    return isset($this->keys[$this->position]) ? $this->keys[$this->position] : null;
  }

  /**
   * (PHP 5 &gt;= 5.1.0)<br/>
   * Move forward to next element
   * @link http://php.net/manual/en/iterator.next.php
   * @return void Any returned value is ignored.
   */
  public function next() {
    ++$this->position;
  }

  /**
   * (PHP 5 &gt;= 5.1.0)<br/>
   * Return the current element
   * @link http://php.net/manual/en/iterator.current.php
   * @return mixed Can return any type.
   */
  public function current() {
    return ($key = $this->key()) === null ? null : $this->list[$key];
  }

  /**
   * (PHP 5 &gt;= 5.1.0)<br/>
   * Offset to retrieve
   * @link http://php.net/manual/en/arrayaccess.offsetget.php
   * @param mixed $offset <p>
   * The offset to retrieve.
   * </p>
   * @return mixed Can return all value types.
   */
  public function offsetGet($offset) {
    return isset($this->list[$offset]) ? $this->list[$offset] : null;
  }

  /**
   * (PHP 5 &gt;= 5.1.0)<br/>
   * Offset to unset
   * @link http://php.net/manual/en/arrayaccess.offsetunset.php
   * @param mixed $offset <p>
   * The offset to unset.
   * </p>
   * @return void
   */
  public function offsetUnset($offset) {
    throw new \InvalidStateException('Unable to remove item of datasource');
  }

  /**
   * (PHP 5 &gt;= 5.1.0)<br/>
   * Offset to set
   * @link http://php.net/manual/en/arrayaccess.offsetset.php
   * @param mixed $offset <p>
   * The offset to assign the value to.
   * </p>
   * @param mixed $value <p>
   * The value to set.
   * </p>
   * @return void
   */
  public function offsetSet($offset, $value) {
    throw new \InvalidStateException('Unable to change item of datasource');
  }

  /**
   * (PHP 5 &gt;= 5.1.0)<br/>
   * Whether a offset exists
   * @link http://php.net/manual/en/arrayaccess.offsetexists.php
   * @param mixed $offset <p>
   * An offset to check for.
   * </p>
   * @return boolean Returns true on success or false on failure.
   * </p>
   * <p>
   * The return value will be casted to boolean if non-boolean was returned.
   */
  public function offsetExists($offset) {
    return array_key_exists($offset, $this->list);
  }
}
