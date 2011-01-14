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
* Direct connection to AP
*/
namespace APos\Connector;

class DirectConnector implements IConnector {
  protected $cache = array();

	public function __construct($options) {
    // Dummy
  }
	
	/**
	* Get class name, which implements APosIf
	*/
	public function getClassName($driver) {
		return "\\APos\\Handlers\\{$driver}Handler";
	}

	/**
	* Check if operating system exists
	* @return bool
	*/
	public function exists($driver) {
		$className = $this->getClassName($driver);
		return class_exists($className, false);
	}
	
  /**
  * Create new client
  * @param string $os Operation system
  * @param int $apid Index of AP
  */
  public function create($driver, $apid) {
    if(!isset($this->cache[$apid])) $this->cache[$apid] = $this->_create($driver, $apid);
    return $this->cache[$apid];
  }

  protected function _create($driver, $apid) {
		$className = $this->getClassName($driver);
		if(!class_exists($className)) throw new \NotFoundException("Connector did not find $className");
		
		return new $className($apid);
	}
}
