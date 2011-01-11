<?php
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
