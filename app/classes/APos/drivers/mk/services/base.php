<?php
namespace APos\Handlers\Mikrotik\Services;

/**
* Base class for Mikrotik services
*/
abstract class APService implements \APos\Handlers\Services\IAPService {
  /** @var APos\Handlers\MkHandler */
	protected $handler;

  /** @var AP */
	protected $ap;

  /** @var string */
	protected $serviceName;


	public function __construct($handler, $serviceName) {
		$this->serviceName = $serviceName;
		
		if(!($handler instanceof \Thrift\APos\MkIf)) throw new \InvalidArgumentException("AP handler must be \\Thrift\\APos\\MkIf");
		$this->handler = $handler;
		$this->ap = $handler->getAP();
	}

  /**
   * @return \Mikrotik\SSHClient
   */
  protected function getSSH() {
    return $this->handler->getSSH();
  }

  /**
   * @return \Mikrotik\RouterOS
   */
  protected function getROS() {
    return $this->handler->getROS();
  }
}
