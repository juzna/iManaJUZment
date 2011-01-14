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

namespace ShapingModule\Drivers;

/**
* Queue simple on Mikrotik
*/
class QueueSimple extends BaseDriver {
  // Internal: list of simple queues to be created
  protected $queueList;

  // Internal: list of commands to be executed
  protected $commands;

  // Internal: were generated commands synced with Mk?
  protected $commandsSynced;



  /**
   * Get interface of APos, which is needed to implements this queues
   * @return string
   */
  function getRequiredAPosInterfaceName() {
    return "\\Thrift\\APos\\MkIf";
  }

  /**
   * Generate list of commands
   */
  protected function generateCommands($sync) {
    if(empty($this->queueList)) $this->generateQueueList();

    if($sync) {
      $this->commandsSynced = true;
      $this->generateSyncedCommands();
    }
    else {
      $this->commandsSynced = false;
      $this->generateAddCommands();
    }
  }

  /**
   * Generate commands which will synchronize with MK
   */
  protected function generateSyncedCommands() {
    // TODO:
  }

  /**
   * Generate commands for new MK
   */
  protected function generateAddCommands() {
    $this->commands = array_map(function($item) { $item[0] = 'add'; return $item; }, $this->queueList);
  }

  /**
   * Generate list of simple queues which should exist on router
   */
  protected function generateQueueList() {
    // TODO:
  }


  /**
   * Preview commands to be sent
   * @param bool $sync Show commands for sync, not for new config
   * @return string
   */
  function preview($sync = false) {
   if(empty($this->commands)) $this->generateCommands($sync);

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
   * Synchronize it
   */
  function commit() {
    // Clear cached commands if they're not synced
    if(is_bool($this->commandsSynced) && !$this->commandsSynced) $this->commands = null;

    // Generate commands if needed
    if(empty($this->commands)) $this->generateCommands(true);

		// Prepare API commands (for using via Thrift)
		$apiCommands = array();
		foreach($this->commands as $row) {
			$cmd = array_shift($row);

			$apiCommands[] = array(
				'path'		=> 'queue simple',
				'command'	=> $cmd,
				'params'	=> $row,
			);
		}

    // Execute
    $ret = $this->apos->executeAPIMulti($apiCommands);

    // TODO: validate result
    return $ret ? 1 : 1;
  }






}

class XXX {
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

