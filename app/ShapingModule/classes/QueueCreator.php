<?php

namespace ShapingModule;

/**
 * Creator of abstract queues
 */
class QueueCreator extends \Nette\Object {
  /** @var string */
  private $driverName;

  /** @var \ShapingModule\Drivers\IShapingDriver */
  private $driver;

  /** @var string */
  private $connectorName;

  private $connector;

	private $queueByParent = array();
	private $queueById = array();
	private $ipUsed = array();

	// Defaultni rychlost
	private $defaultSpeed = array(
		'rxmin' => '10k',    	'txmin' => '10k', 
		'rxmax' => '2M', 	'txmax' => '512k', 
		'rxburst' => '0', 	'txburst' => '0',
		'rxtresh' => '0', 	'txtresh' => '0',
		'rxtime' => '0',  	'txtime' => '0',
		'rxpriority' => 7,	'txpriority' => 7,
	);

  /**
   * Prepare creator
   * @param string $driverName Name of driver, from which is created class name for driver
   */
  public function __construct($driverName, $connectorName = 'thrift') {
    $this->driverName = $driverName;
    $this->connectorName = $connectorName;

    if(!class_exists($driverClass = $this->getDriverClass($driverName))) throw new \InvalidArgumentException("Shaping driver class '$driverClass' not exists");
  }

  /**
   * Get class name of driver
   */
  protected function getDriverClass($driverName) {
    return "ShapingModule\\Drivers\\{$driverName}Driver";
  }

  /**
   * Get used driver
   * @return \ShapingModule\Drivers\IShapingDriver
   */
  public function getDriver() {
    if(!isset($this->driver)) $this->driver = $this->createDriver();
    return $this->driver;
  }

  /**
   * Get connector to drivers
   */
  public function getConnector() {
    if(!isset($this->connector)) $this->connector = $this->createConnector();
    return $this->connector;
  }

  protected function createConnector() {

  }

  protected function createDriver() {
    $cls = $this->getDriverClass($this->driverName);
    return new $cls($this->getConnector());
  }



	/**
	* Prepare queues for shaper
	* @param int $shaper ID shaperu, pro ktery pripravujeme queue
	* @param string $queueDriver Driver, ktery nastavuje queue (if null, just create queue and not attach driver)
	* @param string $aposConnector Connector pro pripojeni k AP
	* @return \Queue\Drivers\IDriver Driver for creating queues
	*/
	public function prepare(\Shaper $shaper) {

		// Shaper info
		$shaperInfo = mfa("select * from `QueueShaper` where ID='$shaperId'") ?: array(
			'ID'		=> (int) $shaperId,
			'txzbytek'	=> 0,
			'rxzbytek'	=> 0,
			'queuetype'	=> 'default',
			'queuetypezak'	=> 'default',
		);
		
		// Load queues
		$this->queueSettings = q2('parent,', "select * from `Queue` where `shaper`='$shaperId' order by poradi, name");
		
		// Load tariffs
		$this->tarifList = q2('tarif,flag', "select * from `TarifRychlost`");
		
		// Recursivly create queues
		$this->createQueue(0);
		
		// Compact queue structure
		$this->compact();
		
		if($queueDriver) {
			// Initialize driver and APos
			$driver = new $driverClass($shaperInfo);
			$driver->addQueues($this->queueById);
			$driver->setAPos(APos::get($shaperId, $aposConnector)); // Attach APos
			
			return $driver;
		}
	}
	
	/**
	* Dump this queue tree
	*/
	function dump() {
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
	* Recursivly create queues
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
