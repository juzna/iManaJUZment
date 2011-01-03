<?php

namespace APos\Connector;

/**
* Connector
*/
interface IConnector {
	/**
	* Initialize connector
	*/
	public function __construct($options);

	/**
	* Get class name, which implements APosIf
	* @return string
	*/
	public function getClassName($driver);
	
	/**
	* Load driver
	* @return void
	*/
	public function load($driver);
	
	/**
	* Check if operating system exists
	* @return bool
	*/
	public function exists($driver);
	
	/**
	* Create new client
	* @param string $os Operation system
	* @param int $apid Index of AP
	* @return \APos\Handlers\APosIf
	*/
	public function create($driver, $apid);
}

/**
* Server for connectors
*/
interface IConnectorServer {
	/**
	* Create server and execute commands thru handler
	* @param string $driver
	* @param int $apid
	* @param \APos\Handlers\APosIf
	* @return void
	*/
	public function createServer($driver, $apid, $handler);
}

