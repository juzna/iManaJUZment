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
