<?php

namespace APos\Connector;

/**
* Connector
*/
interface IConnector {
	/**
	* Initialize connector
	*/
	function __construct($options);

	/**
	* Get class name, which implements APosIf
	* @return string
	*/
	function getClassName($driver);
	
	/**
	* Check if operating system exists
	* @return bool
	*/
	function exists($driver);
	
	/**
	* Create new client
	* @param string $os Operation system
	* @param int $apid Index of AP
	* @return \APos\Handlers\APosIf
	*/
	function create($driver, $apid);
}
