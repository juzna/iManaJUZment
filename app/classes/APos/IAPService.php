<?php

namespace APOs\Handlers\Services;

interface IAPService {
	/**
	* Create service handler
	*/
	public function __construct($ap, $serviceName);
	
	/**
	* Check if service is running
	* @return bool
	*/
	public function check();
	
	/**
	* Activate service
	*/
	public function activate();
	
	/**
	* Deactivate
	*/
	public function deactivate();
	
	/**
	* Check if this AP supports it
	* @return bool
	*/
	public function isSupported();
}
