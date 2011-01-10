<?php
/**
* Zprostredkovani komunikace systemu s Access Pointy
*/


/**
* Access Point Operating System
*/
abstract class APos {
	// Cache for creates APos
	private static $cache = array();
	
	// Map os to driver name
	public static $os2driver = array(
		'mk3'	=> 'mk',
	);
	
	// Default connector
	public static $defaultConnector = 'direct';
	
	/**
	* Get connector
	*/
	public static function getConnector($name = null, $options = null) {
		static $cache;
		
		if(!$name) $name = self::$defaultConnector;
		if(!isset($cache[$name])) {
			$className = "\\APos\\Connector\\$name";
			if(!class_exists($className)) throw new Exception("APos connector '$name' class not exists");
			
			// Instantiate
			$cache[$name] = new $className($options);
		}
		
		return $cache[$name];
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
	* Get APos client
	* @param int $apid ID of access point
	* @param string $connector Connector to APos service
	*/
	public static function get($apid, $connector_ = null) {
		if(isset(self::$cache[$apid])) return self::$cache[$apid];
		
		// Find OS
		$os = mr("select `os` from AP where ID='$apid'", 'data');
		if(!$os) throw new NotFoundException("AP or it's operating system not found");
		$driver = self::getDriverName($os);
		
		// Get connector implementation
		$connector = ($connector_ instanceof \APos\Connector\IConnector) ? $connector_ : self::getConnector($connector_);
		
		return self::$cache[$apid] = $connector->create($driver, $apid);
	}
	
	/**
	* Flush cache of connected APs
	*/
	public static function flushCache() {
		self::$cache = array();
	}
}

