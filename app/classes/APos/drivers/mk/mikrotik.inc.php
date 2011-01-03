<?
/**
* @package APOS
* @subpackage Mikrotik
*/

class Mikrotik {
	protected $host;
	protected $port = 22;
	protected $user;
	protected $pass;
	
	public $version;
	public $major; // 2.9 or 3
	public $minor; // last number of version
	
	public $ssh; // SSH connection  (class SSH3)
	public $ros = null; // Connection to RouterOS API (class RouterOS)
	
	public $exportMethod; // Method used in export function
	public $exportFromMethod; // Method used in exportFrom function
	
	public static $triggerErrors = false;
	
	// Keys which have boolean values
	public static $booleanKeys = array(
		'dynamic', 'static', 'invalid', 'active', 'enabled', 'disabled', 'passthru', 'running', 'slave', 'radius', 'blocked',
		'connect', 'rip', 'bgp', 'ospf', 'mme', 'blackhole', 'unreachable', 'prohibit'
	);	
	
	/**
	* Create Mikrotik using login data in database
	* @param int $index ID in table helemik.AP
	* @return Mikrotik
	*/
	public static function fromDB($index) {
		$index = (int) $index;
		
		$row = mfo("select * from `AP` where `ID`='$index'", 'data');
		if(!$row) throw new NotFoundException("AP s ID '$index' neexistuje");
		if(!startsWith($row->os, 'mk')) throw new NotFoundException("AP s ID '$index' neni mikrotik");
		
		$mk = new Mikrotik($row->defaultIP);
		$mk->login($row->username, $row->password);
		
		return $mk;
	}
	
	public function __construct($host, $port = null) {
		$this->host = $host;
// 		if($port) $this->port = $port;
		
		// Connect to SSH
		$this->ssh = new SSH3;
		$this->ssh->connect($this->host, $this->port);
		
		$this->ssh->shellPrompt = '|^\\[([a-z]+)[@]([a-z0-9 -]+)\\] ([a-z0-9-_ ]+)> |iUm';
		//$this->ssh->shellPrompt = '|> |iUm';
	}
	
	/**
	* Login to MK via SSH
	*/
	public function login($user, $pass) {
		$this->user = $user;
		$this->pass = $pass;
		
		if(!$ret = $this->ssh->authPass($user, $pass)) return false;
		
		// Get version
		$version = $this->version = $this->ssh->shellCmdWait(':put [/system resource get version]');
		
		// Jiny pokus o zjisteni verze (pokud zvoleny prikaz neexistuje)
		if(startsWith($version, 'input does not match')) {
			$version = $this->version = $this->ssh->shellCmdWait(':put [/system package get system version]');
		}
		
		Assert::preg($version, '/^[23]\.[0-9.]+$/', "Neznama verze");
		if($version[0] == 2) {
			$this->major = substr($version, 0, 3);
			$this->minor = (int) substr($version, 4);
			
			$this->exportMethod = 'print-detail-exec';
			$this->exportFromMethod = 'export-exec';
		}
		elseif($version[0] == 3) {
			$this->major = substr($version, 0, 1);
			$this->minor = (int) substr($version, 2);
			
			if($this->minor >= 21) {
				$this->exportMethod = 'api-query';
				$this->exportFromMethod = 'print-asValue-exec';
			}
			else {
				$this->exportMethod = 'api-find';
				$this->exportFromMethod = 'print-asValue-exec';
			}
		}
		else throw new UnsupportedException("Mikrotik version $version is not supported");
	}
	
	/**
	* Connect to Mikrotik API
	*/
	public function connectRouterOS() {
		if($this->major != 3) throw new Exception("Mikrotik API is not supported in $this->major version");
		if($this->ros) return true;
		$this->ros = new RouterOS($this->host, $this->user, $this->pass, $this->version);
	}
	
	/**
	* Export data from Mikrotik
	* @param string $path Path in MK to export (see mikrotik console commands)
	* @param array $conditions List of conditions [ name, operator, value ]
	* @param string $proplist List of properties to export
	* @param int $timeout Max time to wait in seconds
	* @return array List of found items
	*/
	public function export($path, $conditions = null, $proplist = null, $timeout = 5) {
		switch($this->exportMethod) {
			// Export via Mikrotik API (using find)
			case 'api-find':
				// Connect to API
				$this->connectRouterOS();
				$ret = array();
				foreach($this->ros->find($path, $conditions, $proplist, $timeout) as $item) {
					$id = @$item['.id'];
					if($id) $ret[$id] = $item;
					else $ret[] = $item;
				}
				return $ret;
				
				
			// Export via Mikrotik API using queries
			case 'api-query':
				// Connect to API
				$this->connectRouterOS();
				$ret = array();
				foreach($this->ros->query($path, $conditions, $proplist, $timeout) as $item) {
					$id = @$item['.id'];
					if($id) $ret[$id] = $item;
					else $ret[] = $item;
				}
				return $ret;
				
			
			// Print asValue (available in MK3 via ssh), using separate ssh exec and 'print where' in MK
			case 'print-asValue-exec-where':
			case 'print-asValue-shell-where':
				if($this->major != 3) throw new Exception("'print where' nor 'print as-value' is not supported by MK $this->version");
				
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
				
				// Send command to Mikrotik via SSH
				$cmd = ":put [/$path print as-value without-paging " . ($cond ? "where $cond" : '') . " ]";
				$data = ($this->exportMethod == 'print-asValue-exec-where') ? $this->ssh->execWait($cmd) : str_replace("\n", '', $this->ssh->shellCmdWait($cmd));
				
				return RouterOS::unifyValues(Mikrotik::parse_asValue($data), $this->version);
				
				
			// Print asValue - using separate
			case 'print-asValue-exec-find':
			case 'print-asValue-shell-find':
				if($this->major != 3) throw new Exception("'print as-value' is not supported by MK $this->version");
			
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
				
				// Send command to Mikrotik via SSH
				$cmd = ":put [/$path print as-value without-paging from=[find $cond ] ]";
				$data = ($this->exportMethod == 'print-asValue-exec-find') ? $this->ssh->execWait($cmd) : str_replace("\n", '', $this->ssh->shellCmdWait($cmd));
				
				return RouterOS::unifyValues(Mikrotik::parse_asValue($data), $this->version);
			
			// Print detail command (avail in MK2.9)
			case 'print-detail-exec':
			case 'print-detail-shell':
				// Get filter for MK 2.9 (needed in some command such ad 'ip firewall mangle print')
				if($this->major == 3) $filter = '';
				else $filter = $this->getFilter($path);
				
				// Create conditions
				$cond = '';
				if(is_array($conditions)) foreach($conditions as $_c) $cond .= isset($_c[1]) ? "{$_c[0]}{$_c[1]}\"{$_c[2]}\" " : $_c[0];
				elseif(is_string($conditions)) $cond = $conditions;
				
				// Get ID's
				$cmd = ":put [/$path find $cond]";
				$ids = ($this->exportMethod == 'print-detail-exec') ? $this->ssh->execWait($cmd) : $this->ssh->shellCmdWait($cmd);
				if(empty($ids)) return array();
				
				// Send command to Mikrotik via SSH
				$cmd = "/$path print $filter detail without-paging from=$ids";
				$data = ($this->exportMethod == 'print-detail-exec') ? $this->ssh->execWait($cmd) : $this->ssh->shellCmdWait($cmd);
						
				return RouterOS::unifyValues(Mikrotik::parse_printDetail($data, $ids), $this->version);
			
			// Using export function in MK
			case 'export-exec':
			case 'export-shell':
				if($this->major == 3) throw new UnsupportedException("export method is not supported in MK $this->version"); // Not working because it would export subsections
				if($conditions && $this->major == 3) throw new UnsupportedException("'export from' is not supported in MK $this->version");
				
				// Mikrotik 3 - without from= clause
				if($this->major == 3) {
					$cmd = "/$path export";
					$data = ($this->exportMethod == 'export-exec') ? $this->ssh->execWait($cmd) : $this->ssh->shellCmdWait($cmd);
					
					return Mikrotik::parse_export(null, $data);
				}
				// In MK2.9 use from=[find...]
				else {
					// Create conditions
					$cond = '';
					foreach($conditions as $_c) $cond .= isset($_c[1]) ? "{$_c[0]}{$_c[1]}\"{$_c[2]}\" " : $_c[0];
					
					// Get ID's
					$cmd = ":put [/$path find $cond ]";
					$ids = ($this->exportMethod == 'export-exec') ? $this->ssh->execWait($cmd) : $this->ssh->shellCmdWait($cmd);
					if(empty($ids)) return array();
					
					$cmd = "/$path export from=$ids";
					$data = ($this->exportMethod == 'export-exec') ? $this->ssh->execWait($cmd) : $this->ssh->shellCmdWait($cmd);
					
					return RouterOS::unifyValues(Mikrotik::parse_export($ids, $data));
				}
				
			default:
				throw new Exception("Export method '$this->exportMethod' is not supported");
		}
	}
	
	/**
	* Export data from Mikrotik witch given ID's
	* @param string $path Path in MK to export (see mikrotik console commands)
	* @param array $ids List of item ID's
	* @param string $proplist List of properties to export
	* @param int $timeout Max time to wait in seconds
	* @return array List of found items
	*/
	public function exportFrom($path, $ids = null, $proplist = null, $timeout = 5) {
		switch($this->exportFromMethod) {
			// Export via Mikrotik API (using find)
			case 'api-find':
				throw new UnsupportedException;
				
				
			// Export via Mikrotik API using queries
			case 'api-query':
				throw new UnsupportedException;
				
			// Print asValue - using separate
			case 'print-asValue-exec':
			case 'print-asValue-shell':
				if($this->major != 3) throw new Exception("'print as-value' is not supported by MK $this->version");
			
				// Send command to Mikrotik via SSH
				$cmd = ":put [/$path print as-value without-paging from=$ids ]";
				$data = ($this->exportFromMethod == 'print-asValue-exec-find') ? $this->ssh->execWait($cmd) : str_replace("\n", '', $this->ssh->shellCmdWait($cmd));
				
				return RouterOS::unifyValues(Mikrotik::parse_asValue($data, $ids), $this->version);
			
			// Print detail command (avail in MK2.9)
			case 'print-detail-exec':
			case 'print-detail-shell':
				// Get filter for MK 2.9 (needed in some command such ad 'ip firewall mangle print')
				if($this->major == 3) $filter = '';
				else $filter = $this->getFilter($path);
				
				// Send command to Mikrotik via SSH
				$cmd = "/$path print $filter detail without-paging from=$ids";
				$data = ($this->exportFromMethod == 'print-detail-exec') ? $this->ssh->execWait($cmd) : $this->ssh->shellCmdWait($cmd);
				
				return RouterOS::unifyValues(Mikrotik::parse_printDetail($data, $ids), $this->version);
			
			// Using export function in MK
			case 'export-exec':
			case 'export-shell':
				if($this->major == 3) throw new UnsupportedException("export method is not supported in MK $this->version"); // Not working because it would export subsections
				
				$cmd = "/$path export from=$ids";
				$data = ($this->exportFromMethod == 'export-exec') ? $this->ssh->execWait($cmd) : $this->ssh->shellCmdWait($cmd);
				
				return RouterOS::unifyValues(Mikrotik::parse_export($ids, $data));
				
			default:
				throw new Exception("Export method '$this->exportFromMethod' is not supported");
		}
	}
	
	/**
	* Get filter parameter for print command on console
	* (Needed for some command in 2.9)
	* @param string $path Path to be printed
	* @return string 
	*/
	public function getFilter($path) {
		if($this->major != '2.9') throw UnsupportedException("Filter is valid only for Mikrotik 2.9");
		
		// Normalize path
		{
			if(!is_array($path)) $path = split('[/ ]', $path);
			
			// Remove first empty item
			if(empty($path[0])) array_shift($path);
			
			$path = implode(' ', $path);
		}
		
		switch($path) {
			case 'ip firewall mangle':	return 'all';
			case 'ip firewall nat':		return 'all';
			case 'ip firewall filter':	return 'all';
		}
	}
	
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
	
	/**
	* Provedeme sadu prikazu, cekame na vysledek
	*/
	public function cmdList($cmdList, $delim = '') {
		$ret = array();
		
		// Projdeme jednotlive prikazy
		foreach($cmdList as $index => $cmd) {
			// Volame prikaz
			$data = $this->ssh->shellCmdWait($cmd);
			
			if(empty($delim)) $ret[$index] = $data;
			else {
				// Rozdelime hodnoty
				$data = explode("\n", $data);
				while(list($k, $v) = each($data)) {
					$data[$k] = explode($delim, trim($v));
				}
				$ret[$index] = $data;
			}
		}
		
		return $ret;
	}
	
	/**
	* Provedeme prikaz a vratime vysledek
	* @return string
	*/
	public function cmd($cmd) {
		return $this->ssh->shellCmdWait($cmd);
	}
	
	/**
	* Parse response to command 'print as-value' from Mikrotik
	* @param string $data Data received from console
	* @return array Array of items
	*/
	public static function parse_asValue($data, $ids = null) {
		//var_dump($data);
		if(trim($data) === '') return array(); // No data present
		
		// There's an error
		if(startsWith($data, 'input does not match')) {
			if(self::$triggerErrors) trigger_error("Mikrotik says: $data");
			return array();
		}
		
		$ret = array();
		$list = explode(';', $data);
		
		$actualRow = null;
		$lastName = null;
		
		// Loop thru all key-value pairs and store
		foreach($list as $polozka) {
			@list($name, $value) = $x = explode('=', $polozka, 2);
			
			// Strednik byl navic -> ulozime k predchozi hodnote
			if(sizeof($x) == 1) {
				$actualRow[$lastName] .= ';' . $name;
				continue;
			}
			
			// Je to novy radek
			if($name == '.id') {
				// ulozime puvodni
				if($actualRow) $ret[$actualRow['.id']] = $actualRow;
				
				// Aktualni vypradznime
				$actualRow = array();
			}
			
			// Ulozime hodnotu
			$actualRow[$name] = $value;
			
			// Zapamatujeme si aktualni jmeno pro priste
			$lastName = $name;
		}
		
		// Ulozime posledni radek
		if($actualRow) {
			if(isset($actualRow['.id'])) $ret[$actualRow['.id']] = $actualRow;
			else $ret[] = $actualRow;
		}
		
		
		// Combine with ID's
		if(is_null($ids)) return $ret; // No ID's, just values
		
		// List of ID's
		$idList = split('[;,]', $ids);
		
		// Combine ids and params and return
		if($ret && (sizeof($idList) == sizeof($ret))) return array_combine($idList, $ret);
		else throw new Exception("Invalid export commands: got " . sizeof($idList) . " indexes and " . sizeof($ret) . " items");
	}
	
	/**
	* Parse response to command 'print detail' from Mikrotik
	* @param string $data Data received from console
	* @param string $ids List of items indexes
	* @return array Array of items
	*/
	public static function parse_printDetail($data, $ids = null) {
		//var_dump($data);
		
		if(empty($data)) return array(); // No data
		
		// There's an error
		if(startsWith($data, 'input does not match')) {
			if(self::$triggerErrors) trigger_error("Mikrotik says: $data");
			return array();
		}
	
		$data = str_replace("\r\r\n", "\n", $data); // Make normal eof line
		$items = explode("\n\n", $data); // Split by double new line
		$ret = array();
		
		// It begin with 'Flags:', read them
		$flags = array();
		if(startsWith($items[0], 'Flags: ')) {
			// Remove lines with no leading space as flag line, rest as first item
			{
				$item = $items[0];
				$flagRow = '';
				$firstItem = '';
				$isFlag = true;
				
				foreach(explode("\n", $item) as $row) {
					if($row[0] == ' ' || !$isFlag) {
						$firstItem .= "$row\n";
						$isFlag = false;
					}
					else $flagRow .= "$row\n";
				}
				
				if(!trim($firstItem) && sizeof($items) == 1) return array(); // No items, just flags
				
				$items[0] = $firstItem;
			}
			
			
			if(preg_match_all('/([A-Z]) - ([a-z]+)/', $flagRow, $match)) $flags = array_combine($match[1], $match[2]);
		}
		
		foreach($items as $item) {
			$item = trim($item); //str_replace("\n        ", '', $item));
			if(startsWith($item, 'echo: ')) continue; // Some stupid message
			
			$item .= "\n"; // Add trailing space to store last item before stop
			
			$start = strpos($item, ' ');
			$key = substr($item, 0, $start);
			
			// echo "Key is $key, $item";
			
			$isString = false;
			$paramName = $prev = $tmp = '';
			$itemFlags = $params = array();
			
			$len = strlen($item);
			for($x = $start + 1; $x < $len; $x++) { // Projdeme kazdy znak
				$char = $item[$x];
				$slashed = (bool) ($prev == '\\');
				
				$save = true;
				
				if(($char==' ' or $char=="\r" or $char=="\n") && !$isString) { // Konec parametru
					if($paramName) {
						if(!trim($tmp)) {
							$tmp .= $char;
							continue;
						}
						
						// Ulozeni hodnoty
						if(!isset($params[$paramName])) $params[$paramName] = '';
						$params[$paramName] .= $tmp;
					}
					elseif(trim($tmp) !== '') {
						$itemFlags[] = trim($tmp);
					}
					
					$tmp = $char;
				}
				
				// Zadan nazev parametru
				elseif($char == '=') {
					$paramName = ltrim($tmp);
					$tmp = '';
				}
				
				// Hranice stringu
				elseif($char == '"' && !$slashed) {
					if($isString) {
						$isString = false;
					} else {
						$isString = true;
					}
				}
				
				// Backslash
				elseif($char == '\\' && !$slashed) {
				}
				
				// Znak
				else {
					$tmp .= $char;
				}
				
				$prev = $char;
			}
			
			// Nastavime flagy
			if($flags) {
				// Existujici
				foreach($itemFlags as $v) if(isset($flags[$v])) $params[$flags[$v]] = 'true';
				
				// Implicitni
				foreach($flags as $v) if(!isset($params[$v])) $params[$v] = 'false';
			}
			
			if(is_null($key)) {
				trigger_error("Klic neni nastaven\n" . $item);
				continue;
			}
			
			if(isset($ret[$key])) {
				trigger_error("Klic $key uz existueje!\n" . $item);
				continue;
			}
			
			$ret[trim($key, ' ')] = $params;
		}
		
		// Combine with ID's
		if(is_null($ids)) return $ret; // No ID's, just values
		
		// List of ID's
		$idList = split('[;,]', $ids);
		
		// Combine ids and params and return
		if($ret && (sizeof($idList) == sizeof($ret))) return array_combine($idList, $ret);
		else throw new Exception("Invalid export commands: got " . sizeof($idList) . " indexes and " . sizeof($ret) . " items");
	}
	
	/**
	* Parse response to command 'export' from Mikrotik
	* @param string $ids List of IDs which are exported
	* @param string $data Data received from console
	* @return array Array of items
	*/
	public static function parse_export($ids, $data) {
		// Get parameters
		$paramList = Mikrotik::parse_commands($data);
		unset($paramList[0]);
		while(list($key) = each($paramList)) unset($paramList[$key][0]);
		
		if(is_null($ids)) return $paramList; // No ID's, just values
		
		// List of ID's
		$idList = split('[;,]', $ids);
		
		// Combine ids and params and return
		if($paramList && (sizeof($idList) == sizeof($paramList))) return array_combine($idList, $paramList);
		else throw new Exception("Invalid export commands: got " . sizeof($idList) . " indexes and " . sizeof($paramList) . " items");
	}
	
	/**
	* Split command list in one string to array of commands
	* @param string $data Command list from MK console
	* @return array of string List of commands
	*/
	public static function parse_cmdList($data) {
		$cmdIndex = 0; // Index of processed command
		$ret = array();
		
		// Pocess each line
		foreach(explode("\n", $data) as $line) {
			if(!$line = trim($line)) continue;
			if($line[0] == '#') continue; // Comment -> skip it
			
			$continuous = (substr($line, -1) == '\\');
			if($continuous) $line = substr($line, 0, -1); // Remove trailing backslash
			
			// Store command
			@$ret[$cmdIndex] .= $line;
			
			if(!$continuous) $cmdIndex++;
		}
		
		return $ret;
	}
	
	/**
	* Parse commands in string into list of commands represented as arrays (eg [ add, chain => prerouting .... ])
	* @param string $data Commands from MK console
	* @return array of arrays
	*/
	public static function parse_commands($data) {
		// TODO: should be rewritten
		$cmd_list = Mikrotik::parse_cmdList($data);
		$ret_list = array();
	
		// Rozparsujeme prikazy
		foreach($cmd_list as $k => $line) {	
			$line .= "\n"; // Ukoncovaci znak
	
			$is_command = false;
			$is_string = false;
			$command = $param_name = $prev = $tmp = '';
			$params = $m = $m2 = array();
			
			$len = strlen($line);
			for($x = 0; $x < $len; $x++) { // Projdeme kazdy znak
				$znak = $line{$x};
				$slashed = (bool) ($prev=='\\');
				
				$save = true;
				
				if(($znak==' ' or $znak=="\r" or $znak=="\n") && !$is_string) { // Konec parametru
					if($is_command) { // Skoncil nam prikaz
						$command = $tmp;
						$is_command = false;
						
					
					} else { // Skoncil nam parametr
						// Ulozime jej
						if($param_name) $params[$param_name] = $tmp;
						else $params[] = $tmp;
						
						$param_name = '';
					}
					$tmp = '';
					
				} elseif($znak=='=') { // Zadan nazev parametru
					$param_name = $tmp;
					$tmp = '';
					
				} elseif($znak=='"' && !$slashed) { //Hranice stringu
					if($is_string) {
						$is_string = false;	
					} else {
						$is_string = true;
					}
				
				} elseif($znak=='\\' && !$slashed) { //Backslash
					$save = false;
				
				} else { // Znak
					$tmp .= $znak;
					$save = false;
				}
				
			
				$prev = $znak;
			}
			
			$ret_list[$k] = $params;
		}
		
		return $ret_list;
	}
}