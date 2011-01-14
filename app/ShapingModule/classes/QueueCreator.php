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


namespace ShapingModule;

/**
 * Creator of abstract queues
 */
class QueueCreator extends \Nette\Object implements IQueueCreator {
  private $shaper;

  // Map: id -> flag -> Tariff (loaded from database)
  private $tariffMap;

  // Map: parent, index -> ShaperQueue (loaded from database)
  private $queueMap;

  // Internal variables
	private $queueByParent = array();
	private $queueById = array();
	private $ipUsed = array();

	// Default speed
	private $defaultSpeed = array(
		'rxmin' => '10k',   'txmin' => '10k',
		'rxmax' => '2M', 	  'txmax' => '512k',
		'rxburst' => '0', 	'txburst' => '0',
		'rxtresh' => '0', 	'txtresh' => '0',
		'rxtime' => '0',  	'txtime' => '0',
		'rxpriority' => 7,	'txpriority' => 7,
	);




  /**
   * Get list of prepared queues
   * @return array
   */
  function getQueues() {
    // TODO: Implement getQueues() method.
  }

  /**
   * Create queues for given shaper
   * @param Shaper $shaper
   * @return void
   */
  function create(\Shaper $shaper) {
    $this->shaper = $shaper;
    $this->loadConfig();

    // Create queues recursively
    $this->createQueues(0);

    // Compact queue structure
    $this->compact();
  }

  /**
   * Clean any state
   * @return void
   */
  function clean() {
    $this->shaper = $this->queueSettings = $this->tariffMap = null;
    $this->queueByParent = $this->queueById = $this->ipUsed = null;
  }



  /*******   Internal functions  *********/

  /**
   * Loads configuration from database
   */
  protected function loadConfig() {
    // Load queue map
    foreach($this->shaper->queues as $queue) {
      // TODO:
    }
    
    // Loads tariff map
    // TODO:
  }
	
	/**
	* Dump this queue tree
	*/
	public function dump() {
		echo '<table>';
		@$this->_dump();
		echo '</table>';
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
	
	/**
	* Add new queue
	* @param string $parent ID of parent queue (or zero if top-most)
	* @param string $id ID of new queue
	* @param array $params Parameters for this queue
	*/
	private function addQueue($parent, $id, $params) {
		$q = $params;
		$q['.id'] = $id;
		$q['parent'] = $parent;
		
		// Save
		$this->queueByParent[$parent][] = &$q;
		$this->queueById[$id] = &$q;
	}
	
	/**
	* Recursively create queues
	* @param string $parent ID of parent queue (or zero if top-most)
	*/
	function createQueue($parent) {
		if(!isset($this->queueSettings[$parent])) return false; // No settings for this parent
		
		// Create queues
		foreach($this->queueSettings[$parent] as $q) {
			// Add queue
			$this->addQueue($parent, $q['ID'], $q);
			
			// Add recursive
			if(isset($this->queueSettings[$q['ID']])) $this->createQueue($q['ID']);
			
			// Add customers
			if($q['AP']) $this->createQueueCustomers($q, $q['AP'], $q['interface']);
		}
	
	}
	
	/**
	* Vytvor queue-tree pro zakazniky v existujici queue
	* @param array $q Queue info (from table Queue)
	* @param int $ap AP ID
	* @param string $if Interface, may be null
	*/
	function createQueueCustomers($q, $ap, $if) {
		// Parent for customers
		$p = $q['ID'] . '-cust';
		
		$allowCust = ($q['txallow'] && $q['txcustomers']) || ($q['rxallow'] && $q['rxcustomers']);
		$allowGarant = ($q['rxallow'] && !$q['txcustomers']) || ($q['txallow'] && !$q['rxcustomers']);
		
		if($allowCust) {
			// Pridame prime zakazniky
			$sql = "select * from `ZakaznikIP` where `l3parent`='$ap'" . ($if ? " and `l3parentif`='$if'" : '') . " order by inet_aton(ip)";
			$res = q($sql);
			
			while($row = mfo($res)) $this->addCustomerQueue($q, $p, $row);
			
			// Pridame child AP
			$sql = "select id from AP where `l3parent`='$ap'" . ($if ? " and `l3parentif`='$if'" : '');
			$res = q($sql);
			while($row = mfo($res)) {
				$this->createQueueCustomers($q, $row->id, null);
			}
		}
		
		// Garant customers -> add subnets
		if($allowGarant) {
			$q2 = &$this->queueById[$q['ID']];
			
			// Given AP and interface -> find routes thru this if
			if($if) {
				// TODO: rework
				$q2['subnets'] = array('192.168.1.0/24', '192.168.3.0/24');
			}
			
			// Given whole AP -> find routes from l3parent to this
			else {
				// TODO: rework
				$q2['subnets'] = array('192.168.1.0/24', '192.168.3.0/24');
			}
		}
		
		// Add parent for customer's queues
		if(isset($this->queueByParent[$p]) && !isset($this->queueById[$p])) {
			$this->addQueue($q['ID'], $p, array(
				'.type'		=> 'virtual',
				'name'		=> $q['name'] . ' - customers',
				'artificial'	=> 1,
			));
		}
	}
	
	/**
	* Prida zakaznika do queue-tree
	* @param array $q Queue info (from table Queue)
	* @param string $p ID of parent for this queue
	*/
	private function addCustomerQueue($q, $p, $row) {
		if(isset($this->ipUsed[$row->id])) return; // Already set
		
		// Get speed; TODO: make this faster with less queries!
		$tarif = mr("select tarif from `ZakaznikTarif` where porcis='$row->porcis' and `zakladni`=1 and `aktivni`=1");
		$flag = mr("select `tarifflag` from `APSwIf` where AP='$row->l3parent' and `interface`='$row->l3parentif'");
		$speed = @$this->tarifList[$tarif][$flag] ?: @$this->tarifList[$tarif][1] ?: $this->defaultSpeed;
		
		$params = array(
			'.type'	=> 'customer',
			'name'	=> "Zakaznik $row->ip",
			'ip'	=> $row->ip,
		);
		
		$allowTx = $q['txallow'] && $q['txcustomers'];
		$allowRx = $q['rxallow'] && $q['rxcustomers'];
		
		
		// Add speed
		foreach($speed as $k => $v) {
			$s = substr($k, 0, 2);
			if((!$allowTx || $s != 'tx') && (!$allowRx || $s != 'rx')) continue;
			
			$params[$k] = ($row->vlastnirychlost && isset($row->$k)) ? $row->$k : $v;
		}
		
		$this->addQueue($p, 'cust-' . $row->id, $params);
		$this->ipUsed[$row->id] = true;
	}
	
	/**
	* Compact queue structure
	* Remove artificial nodes which are not needed
	*/
	private function compact() {
		foreach($this->queueByParent as $parent => $list) {
			if(sizeof($list) == 1 && !empty($list[0]['artificial'])) $this->reduceQueue($list[0]['.id'], $parent);
		}
	}
	
	/**
	* Delete queue by moving all it's items to another one
	* @param string $from ID of queue, which will be deleted
	* @param string $to ID of destination queue
	*/
	private function reduceQueue($from, $to) {
		// echo "Reducing " . $this->queueById[$from]['name'] . " to " . $this->queueById[$to]['name'] .  "\n";
		
		$old = $this->queueByParent[$from];
		unset($this->queueByParent[$from]); // Remove this subtree
		
		// Move all to new parent
		foreach($old as &$item) {
			$item['parent'] = $to;
			$this->queueByParent[$to][] = $item;
		}
		
		// Remove old node from tree
		$myId = $this->queueById[$from]['.id'];
		foreach($this->queueByParent[$this->queueById[$from]['parent']] as $k => $v) {
			if($v['.id'] == $myId) unset($this->queueByParent[$this->queueById[$from]['parent']][$k]);
		}
	}
}
