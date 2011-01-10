<?php

namespace APos\Connector;



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
	function createServer($driver, $apid, $handler);
}

