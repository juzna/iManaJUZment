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

abstract class BaseDriver implements IShapingDriver {
  protected $apos;

  // Map: parent -> index -> Queue
  protected $queueByParent = array();

  // Map: id -> Queue
  protected $queueById = array();

  

  /**
   * Store connected APos driver
   */
  function setAPOsHandler(\Thrift\APos\APosIf $apos) {
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
