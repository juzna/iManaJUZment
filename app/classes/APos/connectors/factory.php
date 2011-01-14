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


namespace APos\Connector;


class Factory {
  /** @var array Cache of created connectors */
  protected static $cache = array();

	// Map os to driver name
	public static $os2driver = array(
		'mk3'	=> 'mk',
	);

	// Default connector
	public static $defaultConnector = 'direct';


  function __construct() {
    throw new \InvalidStateException("Cannot instantiate static class");
  }

  /**
  * Get driver name for given operating system
  * @param string $os Operating system of AP
  * @return string
  */
  public static function getDriverName($os) {
    return isset(self::$os2driver[$os]) ? self::$os2driver[$os] : $os;
  }

  /**
   * Get class name of a connector
   * @param string $name
   * @return string
   */
  protected static function getClassName($name) {
    return '\\APos\\Connector\\' . ucfirst($name) . 'Connector';
  }

  /**
   * Get connector
   * @return APos\Connector\IConnector
   */
  public static function getInstance($name = null, $options = null) {
    if($name instanceof IConnector) return $name; // Existing connector already given
    if(!isset($name)) $name = self::$defaultConnector;

    // Create instance
    if(!isset(self::$cache[$name])) {
      $className = self::getClassName($name);
      if(!class_exists($className)) throw new \NotFoundException("APos connector '$name' not exists (class '$className' not found)");

      // Instantiate
      self::$cache[$name] = new $className($options);
    }

    return self::$cache[$name];
  }

  /**
   * Connect to APos
   * @param AP $ap
   * @param string|IConnector $connectorName
   * @return \Thrift\APos\APosIf
   */
  public static function connect(\AP $ap, $connectorName = null) {
    return self::getInstance($connectorName)->create(self::getDriverName($ap->os), $ap->ID);
  }
}
