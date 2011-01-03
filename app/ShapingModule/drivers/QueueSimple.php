<?php
namespace ShapingModule\Drivers;

/**
* Queue simple on Mikrotik
*/
class QueueSimple extends BaseDriver {
	private $qsimpeList;
	private $commands;
	
	/**
	* Get interface of APos, which is needed to implements this queues
	* @return string
	*/
	public function getAPosInterfaceName() {
		return "\\Thrift\\APos\\MkIf";
	}
	
	/**
	* Show commands for preview
	* @param bool $synced Show commands for sync, not for new config
	* @return string
	*/
	public function previewCommands($synced = false) {
		// Generate commands
		if(!isset($this->commands)) $this->generateCommands($synced);
		
		// Convert commands array to strings
		$ret = array_map(function($item) {
			$cmd = $item[0];
			$ret = "$cmd ";
			foreach($item as $k => $v) if($k !== 0 && isset($v)) $ret .= "$k=\"$v\" ";
			return $ret;
		}, $this->commands);
		
		return implode("\n", $ret);
	}
	
	/**
	* Commit changes thru APos
	*/
	public function commit() {
		// Prepare commands
		$this->generateCommands(true);
		
		// Prepare API commands
		$apiCommands = array();
		foreach($this->commands as $row) {
			$cmd = array_shift($row);
			
			$apiCommands[] = array(
				'path'		=> 'queue simple',
				'command'	=> $cmd,
				'params'	=> $row,
			);
		}
		
		// Execute it
		$ret = $this->apos->executeAPIMulti($apiCommands);
	}
	
	private function generateCommands($sync = false) {
		// Clear old results
		$this->qsimpeList = $this->commands = null;
		
		// Generate list of simple queues, which are required
		$this->generateQSimpleList();
		
		// Prepare commands
		if($sync) $this->sync();
		else $this->commands = array_map(function($item) { $item[0] = 'add'; return $item; }, $this->qsimpeList);
	}
	
	/**
	* Prepare sync commands
	*/
	private function sync() {
		// Get existing items and index 'em
		$old = indexBy($this->apos->getall('queue simple'), 'name');
		
		// Sync prepared -> old
		foreach($this->qsimpeList as $q) {
			// Already exists -> check for chages
			if(isset($old[$q['name']])) {
				$this->syncItem($old[$q['name']], $q);
			}
			
			// Not exists -> add
			else {
				$this->commands[] = array_merge(array('add'), $q);
			}
			
			// Remove from old list
			unset($old[$q['name']]);
		}
		
		// Sync old -> prepared
		foreach($old as $name => $q) {
			$this->commands[] = array('delete', '.id' => $q['.id']);
		}
	}
	
	/**
	* Sync existing item (check for changes)
	*/
	private function syncItem($old, $new) {
		// Get change list
		$changes = array();
		foreach($new as $k => $v) if($k{0} != '.' && @$old[$k] != $v) $changes[$k] = $v;
		
		if($changes) $this->commands[] = array_merge(array('set', '.id' => $old['.id']), $changes);
	}
	
	/**
	* Generate commands for this shaper
	* @param string $parent
	*/
	private function generateQSimpleList($parent = 0) {
		$pName = ($parent == '0') ? 'none' : $this->queueById[$parent]['name']; // Parent name
		
		foreach($this->queueByParent[$parent] as $q) {
			$type = @$q['.type'];
			
			// Basic params
			$params = array(
				'name'		=> $q['name'],
				'parent'	=> $pName,
				'direction'	=> $this->getQDirection($q),
				'priority'	=> max(@$q['txpriority'], @$q['rxpriority'], 1),
				'queue'		=> $this->shaperInfo['queuetype'] . '/' . $this->shaperInfo['queuetype'],
				'limit-at'	=> $this->getSpeedValue($q, 'min'),
				'max-limit'	=> $this->getSpeedValue($q, 'max'),
				'burst-limit'	=> $this->getSpeedValue($q, 'burst'),
				'burst-threshold'=> $this->getSpeedValue($q, 'tresh'),
				'burst-time'	=> $this->getSpeedValue($q, 'time', 's'),
			);
			
			if($type == 'customer') {
				$params['target-addresses'] = $q['ip'] . '/32';
				$params['queue'] = $this->shaperInfo['queuetypezak'] . '/' . $this->shaperInfo['queuetypezak'];
			}
			
			elseif(!empty($q['subnets'])) {
				$params['target-addresses'] = is_array($q['subnets']) ? implode(',', $q['subnets']) : $q['subnets'];
			}
			
			// Add to queue simple list
			$this->qsimpeList[] = $params;
			
			// Add childs
			if(isset($this->queueByParent[$q['.id']])) $this->generateQSimpleList($q['.id']);
		}
	}
	
	private function getSpeedValue(&$q, $type, $units = null) {
		$tx = !empty($q["tx$type"]) ? $q["tx$type"] : 0;
		$rx = !empty($q["rx$type"]) ? $q["rx$type"] : 0;
		return ($tx || $rx) ? "$tx$units/$rx$units" : null;
	}
		
	
	private function getQDirection($q) {
		$tx = !empty($q['txmax']);
		$rx = !empty($q['rxmax']);
		
		if($tx && $rx) return 'both';
		elseif($tx) return 'upload';
		elseif($rx) return 'download';
	}
}

