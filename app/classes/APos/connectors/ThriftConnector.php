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
* Connect thru Thrift to remote daemon
*/
namespace APos\Connector;

class ThriftConnector implements IConnector, IConnectorServer {
	private $socket;
	private $transport;
	private $protocol;
  protected $cache;
	public $onServerReady;

	public function __construct($options) {
		// Store socket
		if(isset($options['socket'])) {
			if($options['socket'] instanceof \TTransport) $this->socket = $options['socket'];
			else throw new \InvalidArgumentException("Socket is not instance of TTransport");
		}
		
		// Store transport
		if(isset($options['transport'])) {
			if($options['transport'] instanceof \TTransport) $this->transport = $options['transport'];
			else throw new \InvalidArgumentException("Transport is not instance of TTransport");
		}
		
		// Store protocol
		if(isset($options['protocol'])) {
			if($options['protocol'] instanceof \TProtocol) $this->protocol = $options['protocol'];
			else throw new \InvalidArgumentException("Protocol is not instance of TProtocol");
		}
	}
	
	/**
	* Get class name, which implements APosIf
	*/
	public function getClassName($driver) {
		return "\\Thrift\\APos\\{$driver}Client";
	}
	
	/**
	* Check if operating system exists
	* @return bool
	*/
	public function exists($driver) {
		$className = $this->getClassName($driver);
		return class_exists($className);
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
		$className = self::getClassName($driver);
		
		if(!class_exists($className)) throw new \Exception("Connector did not find driver class: '$className'");
		
		// Create socket
		if(!$this->protocol && !$this->transport && !$this->socket) {
			$this->_openSocket($apid);
			if(!$this->socket->isOpen()) throw new \Exception("Thrift daemon apos-$apid is not running (socket not opened)");
		}
		
		// Create transport
		if(!$this->protocol && !$this->transport) $this->transport = new \TBufferedTransport($this->socket, 1024, 1024);
		
		// Create protocol
		if(!$this->protocol) $this->protocol = new \TBinaryProtocol($this->transport);
		
		return new $className($this->protocol);
	}



	private function _openSocket($apid) {
		$unixPath = TMP_DIR . '/sock/apos-' . $apid;
		
		try {
			$this->socket = new \TSocket("unix://$unixPath", -1);
			$this->socket->open();
			return true;
		}
		catch(\TException $e) {
			$code = $e->getCode();
			if($code == 111 || $code == 2) $this->spawn($apid);
			$this->socket->open();
			return true;
		}
	}
	
	/**
	* Spawn new dwarf
	*/
	private function spawn($apid) {
		try {
			$unixPath = TMP_DIR . '/sock/apos-spawner';
			
			$socket = new \TSocket("unix://$unixPath", -1);
			$transport = new \TBufferedTransport($socket, 1024, 1024);
			$protocol = new \TBinaryProtocol($transport);
			$client = new \Thrift\APos\SpawnerClient($protocol);
			
			$transport->open();
			
			$client->spawn($apid);
			
			$transport->close();
		}
		catch(\Exception $e) {
			throw $e;
			throw new \TException("Unable to spawn new APos dwarf", 0, $e);
		}
	}
	
	/**
	* Create server and execute commands thru handler
	* Will create listening socket and process requests from multiple clients
	* @param string $driver
	* @param int $apid
	* @param \APos\Handlers\APosIf
	* @return void
	*/
	public function createServer($driver, $apid, $handler) {
		// Get processor
		$clsProcessor = "\\Thrift\\APos\\{$driver}Processor";
		if(!class_exists($clsProcessor)) $clsProcessor = "\\Thrift\\APos\\APosProcessor"; // generic processor
		$processor = new $clsProcessor($handler);
		
		$unixPath = TMP_DIR . '/sock/apos-' . $apid;
		@unlink($unixPath);
		$socket = new \TServerSocket("unix://$unixPath", -1);
		$socket->listen();
		
		// On ready callback
		if($this->onServerReady) call_user_func($this->onServerReady, $this);
		
		while(true) {
			try {
				$socket->select($processor);
				echo "."; flush();
			}
			catch(\TTransportException $e) {
				return;
			}
		}
	}
	
	/**
	* Create one-way server from existing transport
	* It can server only one client and then finishes
	*/
	public function createServerFromTransport($driver, $transport, $handler) {
		$proto = new \TBinaryProtocol($transport, true, true);
		
		// Get processor
		$clsProcessor = "\\Thrift\\APos\\{$driver}Processor";
		if(!class_exists($clsProcessor)) $clsProcessor = "\\Thrift\\APos\\APosProcessor"; // generic processor
		$processor = new $clsProcessor($handler);
		
		// Create handler
		$processor = new \Thrift\APos\MkProcessor($handler);
		while(true) {
			try {
				$processor->process($proto, $proto);
				echo "."; flush();
			}
			catch(\TTransportException $e) {
				return;
			}
		}
	}
}
