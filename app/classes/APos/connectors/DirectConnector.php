<?php
/**
* Direct connection to AP
*/
namespace APos\Connector;

class DirectConnector implements IConnector {
	public function __construct($options) {}
	
	/**
	* Get class name, which implements APosIf
	*/
	public function getClassName($driver) {
		return "\\APos\\Handlers\\{$driver}Handler";
	}
	
	/**
	* Load driver
	*/
	public function load($driver) {
		// Try directory first
		if(is_dir($dir = APOS_DIR . '/drivers/' . $driver)) {
			if(file_exists($file = "$dir/bootstrap.php")) require_once($file);
			if(file_exists($file = "$dir/$driver.php")) require_once($file);
		}
		
		// Try direct file
		if(file_exists($file = APOS_DIR . '/drivers/' . $driver . '.php')) require_once($file);
	}
	
	/**
	* Check if operating system exists
	* @return bool
	*/
	public function exists($driver) {
		$this->load($driver);
		$className = $this->getClassName($driver);
		
		return class_exists($className, false);
	}
	
	/**
	* Create new client
	* @param string $driver Operation system
	* @param int $apid Index of AP
	*/
	public function create($driver, $apid) {
		$className = $this->getClassName($driver);
		
		if(!class_exists($className, false)) $this->load($driver);
		if(!class_exists($className)) throw new \Exception("Connector didnt find $className");
		
		return new $className($apid);
	}
	
}