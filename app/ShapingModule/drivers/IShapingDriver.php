<?php

namespace ShapingModule\Drivers;

interface IShapingDriver {
	/**
	* Prepare new driver
	* @param array $shaperInfo Shaper info from table QueueShaper
	*/
	function __construct(\Shaper $shaper);
	
	/**
	* Store connected APos driver
	*/
	function setAPos(\Thrift\APos\APosIf $apos);
	
	/**
	* Add queues to internal DB
	* @param array $arr Array of queues
	* @return void
	*/
	function addQueues(array $arr);
	
	/**
	* Get interface of APos, which is needed to implements this queues
	* @return string
	*/
	function getAPosInterfaceName();
	
	/**
	* Show commands for preview
	* @param bool $synced Show commands for sync, not for new config
	* @return string
	*/
	function previewCommands($synced = false);
	
	/**
	* Commit changes thru APos
	*/
	function commit();
}


