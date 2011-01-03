<?php

namespace ShapingModule\Drivers;

abstract class BaseDriver implements IShapingDriver {

	protected $shaperId;
	protected $shaperInfo;
	protected $queueByParent = array();
	protected $queueById = array();
	protected $apos;
	
	/**
	* Prepare new driver
	* @param array $shaperInfo Shaper info from table QueueShaper
	*/
	public function __construct($shaperInfo) {
		$this->shaperInfo = $shaperInfo;
		$this->shaperId = $shaperInfo['ID'];
	}
	
	/**
	* Store connected APos driver
	*/
	public function setAPos(\Thrift\APos\APosIf $apos) {
		// Need mikrotik
		if(!($apos instanceof \Thrift\APos\MkIf)) throw new Exception("APos for QSimple is not mikrotik, cannot proceed");
		
		$this->apos = $apos;
	}
	
	/**
	* Add queues to internal DB
	* @param array $arr Array of queues
	* @return void
	*/
	public function addQueues(array $arr) {
		foreach($arr as $q) {
			$this->queueByParent[$q['parent']][] = &$q;
			$this->queueById[$q['.id']] = &$q;
			unset($q);
		}
	}
	
	/**
	* Dump queue tree
	*/
	public function dump($asReturn = false) {
		if($asReturn) ob_start();
		
		echo '<table>';
		@$this->_dump();
		echo '</table>';
		
		if($asReturn) {
			$ret = ob_get_contents();
			ob_end_clean();
			
			return $ret;
		}
	}
	
	private function _dump($parent = 0, $indent = '') {
		if(isset($this->queueByParent[$parent])) foreach($this->queueByParent[$parent] as $q) {
			echo "<tr id='{$q['.id']}' parent='{$q['parent']}'>
				<td>{$indent}{$q['name']}</td>
				<td>{$q['AP']}:{$q['interface']}</td>
				<td>{$q['rxmin']}/{$q['txmin']}</td>
				<td>{$q['rxmax']}/{$q['txmax']}</td>
			</tr>";
			
			if(isset($q['.id'])) $this->_dump($q['.id'], "$indent&nbsp;&nbsp;&nbsp;");
		}
	}
}
