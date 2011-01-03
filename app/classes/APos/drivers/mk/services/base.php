<?php
namespace APos\Handlers\Mikrotik\Services;

/**
* Base class for Mikrotik services
*/
abstract class APService implements \APos\Handlers\Services\APService {
	protected $apHandler;
	protected $mk;
	protected $api;
	protected $ap;
	protected $serviceName;
	
	public function __construct($apHandler, $serviceName) {
		$this->serviceName = $serviceName;
		
		if(!($apHandler instanceof \Thrift\APos\MkIf)) throw new \InvalidArgumentException("AP handler must be \Thrift\APos\MkIf");
		$this->apHandler = $apHandler;
		$this->ap = $apHandler->ap; //_getAp();
	}
	
	protected function getMk() {
		if(!isset($this->mk)) $this->mk = $this->apHandler->_getMk();
		return $this->mk;
	}
	
	protected function getApi() {
		if(!isset($this->api)) $this->api = $this->apHandler->api; //_getApi();
		return $this->api;
	}
}