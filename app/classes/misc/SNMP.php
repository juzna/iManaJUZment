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


/**
 * SNMP reader
 */
class SNMP {
	private $ip;
	private $community;
	private $ret_list;
	private $typ_list;
	
	public 	$keys = array();
	
	
	function __construct($ip, $community) {
		// Return back the numeric OIDs, instead of text strings.
		snmp_set_oid_numeric_print(true);

		// Zjistime taky typ hodnoty
		snmp_set_quick_print(false);
		
		$this->connect($ip, $community);
	}
	
	// Nastaveni IP a komunity
	function connect($ip, $community) {
		$this->ip = $ip;
		$this->community = $community;
	}

	// Zjisti hodnotu
	function get($oid) {
		return snmpget($this->ip, $this->community, $oid);
	}
	
	
	function walk($oid, &$typ_list = null) {
		$list = @snmprealwalk($this->ip, $this->community, $oid);
		if(!is_array($list)) return false;
		
		$this->typ_list = $this->ret_list = array();
		foreach($list as $oid => $value) {
			@list($typ, $val) = explode(': ', $value, 2);
			if(is_null($val)) { $val = $typ; $typ = ''; }
			
			if(substr($val, 0, 1) == '"') $val = substr($val, 1, -1);

			$this->typ_list[$oid] = strtolower($typ);
			$this->ret_list[$oid] = $val;
		}
		
		$typ_list = $this->typ_list;
		return $this->ret_list;
	}

	// Prevedeme na pole podle zadanych indexu
	function toarray($indexes, $just_values = false) {
		$ret_arr = array();
		
		// Nachystame indexy
		$indexes = explode(';', $indexes);
		$index_list = array();
		foreach($indexes as $v) {
			$v = trim($v);
			if($v==='') continue;
			@list($start, $len, $typ) = explode(',', $v, 3);
		
			$index_list[] = array($start, $len, strtolower($typ));
		}
	
	
		// Projdeme vysledky
		foreach($this->ret_list as $oid => $value) {			
			$oids = explode('.', $oid);
			$oid_len = sizeof($oids);
			
			$typ = $this->typ_list[$oid];
			$ret = $just_values ? $value : array($oid, $typ, $value);
			
			unset($cil);
			$cil = &$ret_arr;
			
			// Pripravime si pole
			foreach($index_list as $row) {
				list($start, $len, $typ) = $row;
				$index = '';
				
				// Dana cast
				$part = $len ? array_slice($oids, $start, $len) : array_slice($oids, $start);
				
				// Provedeme upravu
				switch(true) {
					// Obyc
					case $typ == '':
					default:
						$index = implode('.', $part);
						break;
					
					// MAC
					case $typ == 'mac':
					case $typ == 'm':
						foreach($part as $k => $v) $part[$k] = sprintf('%02X', $v);
						$index = implode($typ == 'm' ? '' : ':', $part);
						break;
					
					// Klic z pole
					case substr($typ, 0, 2) == 'a:':
						$name = substr($typ, 2);
						$index = implode('.', $part);
						if(!empty($this->keys[$name][$index])) $index = $this->keys[$name][$index];
						break;
					
					// Ulozime do vysledku
					case substr($typ, 0, 2) == 's:':
						$name = substr($typ, 2);
						$cil[$name] = implode('.', $part);
						break;
					
				}
				
				// Najdeme dalsi vnorene pole
				if(empty($index)) continue;
				$cil = &$cil[$index];
			}
			
			$cil = $ret;
		}
		
		return $ret_arr;
	}
  
  
  
  /****** Mikrotik specific *****/
  
  /**
   * Get all data from Mikrotik device
   * @param bool $justValues
   * @return bool
   */
  public function mikrotikGetAll($justValues = true) {
    // Simple Queue
    $this->keys['qsimple'] = array(2 => 'name', 'src', 'src-mask', 'dst', 'dst-mask', '', 'bytes-in', 'bytes-out', 'packets-in', 'packets-out');
    if($this->walk('.1.3.6.1.4.1.14988.1.1.2.1.1')===false) return false;
    $ret['qsimple'] = $this->toarray('-1,1; -2,1,a:qsimple', $justValues);
    /**/
  
    // Queue tree
    $this->keys['qtree'] = array(2 => 'name', 'mark', 'parent', 'bytes', 'packets');
    $w = $this->walk('.1.3.6.1.4.1.14988.1.1.2.2.1');
    $ret['qtree'] = $this->toarray('-1,1; -1,1,s:id; -2,1,a:qtree', $justValues);
    /**/
  
    // Interface
    $this->keys['if'] = array(1 => 'id', 'nazev', 'typ', 'mtu', 'speed', 'mac', 'admin-stav', 'oper-stav', 'last-change', 'in-octets', 'in-unicast', 'in-non-unicast', 'in-discard', 'in-error', 'in-unknown-protocol', 'out-octets', 'out-unicast', 'out-non-unicast', 'out-discard', 'out-error', 'out-queue');
    $this->walk('.1.3.6.1.2.1.2.2.1');
    $ret['if'] = $this->toarray('-1,1; -2,1,a:if', $justValues);
    /**/
  
  
    // Wireless
    $this->keys['wireless'] = array(2 => 'tx-rate', 'rx-rate', 'strength', 'ssid', 'bssid', 'frequency', 'band');
    $this->walk('.1.3.6.1.4.1.14988.1.1.1.1.1');
    $ret['wireless'] = $this->toarray('-1,1; -2,1,a:wireless', $justValues);
  
    
    // Regtable
    $this->keys['regtable'] = array(1 => 'mac-address', null, 'strength', 'tx-bytes', 'rx-bytes', 'tx-packets', 'rx-packets', 'tx-rate', 'rx-rate');
    $this->walk('.1.3.6.1.4.1.14988.1.1.1.2.1');
    $ret['regtable'] = $this->toarray('14,6,mac; 13,1,a:regtable', $justValues);
    /**/
  
    
    // Uptime + CPU
    $ret['uptime'] 	= $this->get('.1.3.6.1.2.1.1.3.0');
    $ret['cpu'] 	= $this->get('.1.3.6.1.2.1.25.3.3.1.2.1');
    /**/
  
    // System resource
    $this->keys['resource'] = array(1 => 'index', 'typ', 'nazev', 'allocation', 'size', 'used', 'failures');
    $w = $this->walk('.1.3.6.1.2.1.25.2.3.1');
    $ret['resource'] = $this->toarray('-1,1; -2,1,a:resource', $justValues);
    /**/
    
    // Got it finished
    return $ret;
  }
}
