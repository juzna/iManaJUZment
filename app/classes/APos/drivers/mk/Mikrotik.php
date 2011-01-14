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


namespace Mikrotik;

/**
 * Unified communication with Mikrotik
 *
 * Export method can be:
 *  api-{find, query, getall}
 *  ssh-{printAsValue, printTerse, printDetail, export}-{from,where}-{shell,exec}
 */
class Mikrotik {
  /** @var AP AP info */
  protected $ap;

  /** @var Mikrotik\SSHClient SSH client */
  protected $ssh;

  /** @var Mikrotik\RouterOS  */
  protected $ros;

  /** @var string */
  protected $version;

  /** @var string Method to be used for exporting data */
  protected $exportMethod;

  /** @var string Method to be used for exporting data by IDs */
  protected $exportFromMethod;

  /** @var int Default timeout in seconds */
  public $timeout = 5;

  /** @var bool Show debugging info to stdout*/
  protected $debug = false;

  /** @var array List of supported operating systems of AP */
  public static $supportedOS = array('mk', 'mk3');



  public function __construct(\AP $ap) {
    if(!in_array($ap->os, self::$supportedOS)) throw new \NotSupportedException("Mikrotik driver is not supported for OS '$ap->os'");
    $this->ap = $ap;
  }

  /**
   * Gets SSH client connected to this mikrotik
   * @return Mikrotik\SSHClient
   */
  public function getSSH() {
    if(!isset($this->ssh)) {
      $this->ssh = SSHClient::fromAP($this->ap);
      $this->ssh->setDebug($this->debug);
      if(!isset($this->version)) $this->version = $this->ssh->getVersion();
    }

    return $this->ssh;
  }

  /**
   * Gets RouterOS API client connected to this Mikrotik
   * @return Mikrotik\RouterOS
   */
  public function getROS() {
    if(!isset($this->ros)) {
      $this->ros = RouterOS::fromAP($this->ap, $this->version);
      $this->ros->setDebug($this->debug);
      if(!isset($this->version)) $this->version = $this->ros->getVersion();
    }

    return $this->ros;
  }

  /**
   * Gets version number
   * @return string
   */
  public function getVersion() {
    if(!isset($this->version)) $this->getSSH(); // Connect to SSH to determine version
    return $this->version;
  }

  public function isSSHConnected() {
    return isset($this->ssh);
  }

  public function isROSConnected() {
    return isset($this->ros);
  }


  /*******    Export methods configuration    **********/

  public function setExportMethod($exportMethod) {
    $this->exportMethod = $exportMethod;
    return $this;
  }

  public function setExportFromMethod($exportFromMethod) {
    $this->exportFromMethod = $exportFromMethod;
    return $this;
  }

  public function getExportMethod() {
    if(!isset($this->exportMethod)) $this->exportMethod = $this->_getDefaultExportMethod();
    return $this->exportMethod;
  }

  public function getExportFromMethod() {
    if(!isset($this->exportFromMethod)) $this->exportFromMethod = $this->_getDefaultExportFromMethod();
    return $this->exportFromMethod;
  }

  protected function _getDefaultExportMethod() {
    // TODO:
  }

  protected function _getDefaultExportFromMethod() {
    // TODO:
  }


  /**
   * Prepare conditions for specific version of MK
   */
  protected function unifyConditions($path, $conditions) {
    // TODO: rework

    // Create conditions
    $cond = '';
    if(is_array($conditions)) foreach($conditions as $_c) {
      if(isset($_c[2])) {
        // Unify value
        if(in_array($_c[0], self::$booleanKeys) && in_array($_c[2], array('no', 'false', 'yes', 'true'))) {
          $_c[2] = ($_c[2] == 'no' || $v == 'false') ? 'no' : 'yes';
        }
      }
      $cond .= isset($_c[1]) ? "{$_c[0]}{$_c[1]}\"{$_c[2]}\" " : $_c[0];
    }
    elseif(is_string($conditions)) $cond = $conditions;

  }
  
  /**
   * Post-process returned list
   */
  protected function _postprocess($list) {
    $ret = array();
    
    // Make associative array by IDs
    foreach($list as $item) {
      if(isset($item['.id'])) $ret[$item['.id']] = $item;
      else $ret[] = $item;
    }
    
    return $ret;
  }

  /*********     Export methods    *********/
  
  /**
   * Export whole section (without any conditions)
   * @param string $path Path in MK to export
   * @param int $timeout Timeout in seconds
   * @return array
   */
  public function export($path, $timeout = null, $method = null) {
    if(!isset($timeout)) $timeout = $this->timeout;
    
    @list($method1, $method2, $method3, $method4) = explode('-', $method ?: $method = $this->getExportMethod());
    
    if($method1 == 'api') {
      $ret = $this->getROS()->getall($path, null, $timeout);
    }
    
    elseif($method1 == 'ssh') {
      /** @var $ssh Mikrotik\SSHClient */
      $ssh = $this->getSSH();
      
      switch($method2) {
        case 'printAsVal':
          $ret = $ssh->display($path, 'print-as-value');
          break;
        
        case 'printTerse':
          $ret = $ssh->display($path, 'print-terse');
          break;
          
        case 'printDetail':
          $ret = $ssh->display($path, 'print-detail');
          break;
          
        case 'export':
          $ret = $ssh->display($path, 'print-export');
          break;
      }
    }
    
    if(isset($ret)) return $this->_postprocess($ret);
    else throw new \NotSupportedException("Unknown method: $method");
  }
  
  public function exportByIds($path, $ids, $timeout = null, $method = null) {
    if(!isset($timeout)) $timeout = $this->timeout;
    
    @list($method1, $method2, $method3, $method4) = explode('-', $method ?: $method = $this->getExportMethod());
    
    // Use MK API
    if($method1 == 'api') {
      throw new \NotImplementedException("Dunno how :/");
    }
    
    // Use SSH client
    elseif($method1 == 'ssh') {
      /** @var $ssh Mikrotik\SSHClient */
      $ssh = $this->getSSH();
      $useShell = $method4 == 'shell';
      
      if($method2 == 'printAsVal') $ret = $ssh->printAsValue($path, $ids, $useShell);
      elseif($method2 == 'printTerse') $ret = $ssh->printTerse($path, $ids, $useShell);
      elseif($method2 == 'printDetail') $ret = $ssh->printDetail($path, $ids, $useShell);
      elseif($method2 == 'export') $ret = $ssh->_parseExport($ssh->_execute($useShell, "/$path export from=$ids"));
    }
    
    if(isset($ret)) return $this->_postprocess($ret);
    else throw new \NotSupportedException("Unknown method: $method");    
  }
  
  /**
   * Export section and filter based on conditions
   * @param string $path Path in MK to export
   * @param misc $conditions List of conditions
   * @param int $timeout Timeout in seconds
   * @return array
   */
  public function exportByConditions($path, $conditions, $timeout = null, $method = null) {
    if(empty($conditions)) return $this->export($path, $timeout);
    else $conditions = $this->unifyConditions($path, $conditions);
    
    if(!isset($timeout)) $timeout = $this->timeout;
    
    @list($method1, $method2, $method3, $method4) = explode('-', $method ?: $method = $this->getExportMethod());
    
    // Use MK API
    if($method1 == 'api') {
      if($method2 == 'find') $ret = $this->getROS()->find($path, $conditions, null, $timeout);
      elseif($method2 = 'query') $this->getROS()->query($path, $conditions, null, $timeout);
      elseif($method2 == 'getall') throw new \NotImplementedException;
    }
    
    // Use SSH client
    elseif($method1 == 'ssh') {
      /** @var $ssh Mikrotik\SSHClient */
      $ssh = $this->getSSH();
      $useShell = $method4 == 'shell';
      
      // SSH print from -> will use exportFromIds
      if($method3 == 'from') {
        $ids = $this->getSSH()->_execute($useShell, ":put [/$path find $conditions]");
        return $this->exportByIds($path, $ids, $timeout, $method);
      }
      
      elseif($method2 == 'printAsVal') {
        $cmd = ":put [/$path print as-value without-paging where $conditions ]";
        $ret = $ssh->_parsePrintAsValue($ssh->_execute($useShell, $cmd));
      }
      
      elseif($method2 == 'printTerse') {
        $cmd = "/$path print terse without-paging where $conditions";
        $ret = $ssh->_parsePrintTerse($ssh->_execute($useShell, $cmd));
      }
      
      elseif($method2 == 'printDetail') {
        $cmd = "/$path print detail without-paging where $conditions";
        $ret = $ssh->_parsePrintDetail($ssh->_execute($useShell, $cmd));
      }
      
      elseif($method2 == 'export') {
        $cmd = "/$path export without-paging where $conditions";
        $ret = $ssh->_parseExport($ssh->_execute($useShell, $cmd));
      }
    }
    
    if(isset($ret)) return $this->_postprocess($ret);
    else throw new \NotSupportedException("Unknown method: $method");
  }
  


  /****   Reimport section  *****/
  

	/**
	* Import dat s porovnanim existujicich
	* @param string $path Cesta, ktera se ma exportovat
	* @param string $podm Podminka pro prikaz v MK
	* @param array $arr Pole s novymi udaji
	* @param bool $removeNoSet Zda se maji odstranit ty, ktere nejsou nastavene
	* @param array $info Vystupni informace
	*/
	function import($path, $podm, $arr, $removeNoSet = false, &$info = null) {
		$info = array();
		
		// Zkusime najit parametry pro optimalizaci
		{
			$proplist = array();
			
			// Spocitame pocet jednotlivych signatur
			$optimizeCount = array();
			foreach($arr as $item) {
				if(empty($item['_sig'])) continue;
				
				if(!is_array($sig = $item['_sig'])) $sig = explode(',', $sig);
				
				foreach($sig as $sigName) if($sigName) @$optimizeCount[$sigName]++;
				
				// Mark all values
				foreach($item as $k => $v) if($k[0] != '_') $proplist[$k] = true;
			}
			
			if($optimizeCount) {
				// Vezmeme jen ty nejvetsi
				$optimizeMaxParam = max($optimizeCount);
				foreach($optimizeCount as $k => $v) if($v < 100 || $v < $optimizeMaxParam / 2) unset($optimizeCount[$k]);
				
				// Seradime podle cetnosti
				asort($optimizeCount, SORT_NUMERIC);
				$optimizeNeeded = array_reverse(array_keys($optimizeCount));
				@list($optimize1, $optimize2) = $optimizeNeeded;
			}
			else {
				$optimize1 = $optimize2 = null;
			}
		}

		// Najdeme existujici polozky
		$proplist = implode(',', array_keys($proplist));
		if(!is_array($ex = $this->export($path, $podm, $proplist, 30))) {
			echo "<pre><b>Nedostali jsme pole, ale $ex</b>"; var_dump($ex); throw new Exception("eee");
		}
		
		// Upravime je do optimalizovaneho pole
		$optimizedEx = array();
		reset($ex);
		if($optimize1) while(list($index, $item) = each($ex)) {
			// Get values of optimalized cols
			$val1 = ($optimize1 && isset($item[$optimize1])) ? $item[$optimize1] : '--NULL--';
			$val2 = ($optimize2 && isset($item[$optimize2])) ? $item[$optimize2] : '--NULL--';
			
			if($optimize2) $optimizedEx[$val1][$val2][$index] = &$ex[$index];
			else $optimizedEx[$val1][$index] = &$ex[$index];
		}
		if($optimize1) echo "Optimalizovano podle '$optimize1'" . ($optimize2 ? " a '$optimize2'" : '') . "\n";
		
		$cmd_list = array(); // Seznam prikazu
		$remove_list = array(); // Seznam polozek, co se maji smazat
		
		
		// Projdeme vsecky nove polozky a hledame, na co se daji pouzit
		$celkem = sizeof($arr);
		$counter = 0;
		foreach($arr as $index => $item) {
			$counter++;
			//if($counter % 100 == 0) echo "Porovnavam $counter / $celkem\n";

			// Vyznamna pole
			if(!isset($item['_sig'])) {
				$importItems[] = $item;
				continue;
			}
			if(!is_array($sig = $item['_sig'])) $sig = explode(',', $sig);


			// Vytvorime prohledavaci oblast
			unset($search);
			if($optimize1) {
				// Get values of optimalized cols
				$val1 = ($optimize1 && isset($item[$optimize1])) ? $item[$optimize1] : null;
				$val2 = ($optimize2 && isset($item[$optimize2])) ? $item[$optimize2] : null;

				if($optimize2) {
					if(!$val1) $search = &$ex;
					elseif(!$val2) $search = isset($optimizedEx[$val1]) ? call_user_func_array('array_merge_assoc', @$optimizedEx[$val1]) : null;
					else $search = &$optimizedEx[$val1][$val2];
				}
				else {
					if(!$val1) $search = &$ex;
					else $search = &$optimizedEx[$val1];
				}

			} else $search = &$ex; // Not optimalized

			// Projdeme vsechna pole z exportu a zjistima zda na nektere sedi
			$match = false;
			if(is_array($search)) foreach($search as $index2 => $exItem) {
				$match2 = true;

				// Kontrolujeme vsecka vyznamna pole
				foreach($sig as $field) if(@$exItem[$field] != @$item[$field]) {
					$match2 = false;
					break;
				}

				// Sedi na toto
				if($match2) {
					// Skip it
					if(!empty($item['_skip'])) {
						unset($ex[$index2]);
					}

					// Jiz sedelo drive na neco jineho, smazeme
					elseif($match) {
						$remove_list[] = $index2;
						unset($ex[$index2]);

						//$info['remove'][] = $exItem;
					}

					// Zmenime
					else {
						// Zrusime z exportu
						unset($ex[$index2]);
						$match = true;

						// Policka co se nemeni ztusime
						foreach($item as $k => $v) {
							if(in_array($k, array('disabled', 'passthrough'))) {
								if($v2 = @$exItem[$k]) {
									$v2 = ($v2 == 'true' || $v2 == 'yes') ? 'yes' : 'no';
									$exItem[$k] = $v2;
								}
								$v = ($v == 'true' || $v == 'yes') ? 'yes' : 'no';
								if(!isset($exItem[$k])) unset($item[$k]);
							}
							if(@$exItem[$k] == $v || substr($k, 0, 1)=='_') unset($item[$k]);
						}

						// Jsou nejake zmeny
						if($item) {
							$cmd_list[] = mikrotik_make_cmd("set $index2", $item);
							//print_r($exItem); print_r($item);
							
							
							//$info['set'][$index2] = $item;
						}
					}
				}
			}

			// Nesedi na zadne -> pridame nove
			if(!$match && empty($item['_skip'])) {
				$cmd_list[] = mikrotik_make_cmd('add', $item);
				//$info['add'][] = $item;
			}
		}

		// Smazeme vsecky, co nebyly nastavene
		if($removeNoSet) foreach($ex as $index => $v) {
			$remove_list[] = $index;
			//$info['remove'][] = $v;
		}

		// Mame polozky pro odstraneni
		if($remove_list) array_unshift($cmd_list, "remove " . implode(',', $remove_list));

		// Zadne zmeny
		if(empty($cmd_list)) return true;

		// Presun do slozky
		array_unshift($cmd_list, "/$path");

		return $cmd_list;
	}
	
	public function importPrepare($path, $arr, $ex) {
		$cmd_list = array();
		$remove_list = array();
		
		// Porovname s aktualnim exportem
		foreach($arr as $index => $row) {
			if(empty($row) && !is_array($row)) { // tato polozka je prazdna -> smazeme
				$remove_list[] = $index;

			} elseif(!isset($ex[$index])) { // Chybi v exportu ->pridame
				$cmd_list[] = mikrotik_make_cmd('add', $row);

			} else { // Upravime
				// Policka co se nemeni ztusime
				foreach($row as $k=>$v) {
					$v2 = @$ex[$index][$k];
					
					$v2 = ($v2 == 'true' || $v2 == 'yes') ? 'yes' : 'no';
					$v = ($v == 'true' || $v == 'yes') ? 'yes' : 'no';
					
					if($v2 == $v || substr($k, 0, 1)=='_') unset($row[$k]);
				}
				if(empty($row)) continue; // Zadne pole se nemeni

				$cmd_list[] = mikrotik_make_cmd("set $index", $row);
			}
		}

		// Mame polozky pro odstraneni
		if($remove_list) {
			array_unshift($cmd_list, "remove " . implode(',', $remove_list));
		}
		return $cmd_list;
	}



  public function setDebug($debug) {
    $this->debug = $debug;
    if(isset($this->ssh)) $this->ssh->setDebug($debug);
    if(isset($this->ros)) $this->ros->setDebug($debug);
  }

  public function getDebug() {
    return $this->debug;
  }
}