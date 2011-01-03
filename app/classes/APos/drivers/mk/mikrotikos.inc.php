<?
/**
* @package APOS
* @subpackage Mikrotik
*/

// Trida pro pripojeni do MK a praci s nim
class mikrotikos {
	private $host;
	private $port;
	private $user;
	private $password;
	private $_export;
	public $debug = false;


	/**
	* SSH spojeni
	* @var mySSH2
	*/
	public $ssh = null;

	/**
	* Zda se jedna o MK v 3
	* @var bool
	*/
	public $is3 = false;


	/**
	* Pripojeni na MK
	*/
	public function __construct($host, $port = 22, $user = '', $password = '') {
		$this->host = $host;
		$this->port = $port;
		
		$this->connect();
		
		// Prihlaseni
		if($user) $this->login($user, $password);
	}
	
	// Connect to SSH
	public function connect() {
		if(isset($this->ssh)) throw new Exception("Already connected");
		
		// Pripojime se
		$ssh = new mySSH2();
		$ssh->connect($this->host, $this->port);
		
		// $ssh->shellPrompt = '|^\\[([a-z]+)[@]([a-z0-9 -]+)\\] ([a-z0-9-_ ]+)> |iUm'; // NOT WORKING
		$ssh->shellPrompt = '|> |iUm';
		// $ssh->eol = "\n";
		
		// Store this SSH to this object
		$this->ssh = $ssh;
	}
	
	public function disconnect() {
		// Destroy SSH connection
		$this->ssh = null;
	}
	
	public function reconnect() {
		$this->disconnect();
		$this->connect();
		
		// Login
		if($this->user && $this->password) $this->ssh->authPass($this->user, $this->password);
		
		// Set debug mode
		$this->ssh->setDebug($this->debug);
	}
	
	public function setDebug($mode) {
		$this->debug = $mode;
		$this->ssh->setDebug($mode);
	}
	
	private function debug($text) {
		if($this->debug) echo date('Y-m-d H:i:s') . "\t$text\n";
	}

	/**
	* Prihlaseni
	*/
	public function login($user, $password) {
		$this->user = $user;
		$this->password = $password;

		return $this->ssh->authPass($user, $password);
	}

	/**
	* Provedeme prikaz a vratime vysledek
	* @return string
	*/
	public function cmd($cmd) {
		return $this->ssh->shellCmdWait($cmd);
	}

	/**
	* Provedeme prikaz, ale necekame na vysledek
	* @return stream
	*/
	public function cmdNoWait($cmd) {
		return $this->ssh->exec($cmd);
	}

	public function cmdShell($cmd) {
		return $this->ssh->shellCmdWait($cmd);
	}
	
	/**
	* Execute command and parse result using preg patterns
	* @param string $cmd Command to be executed
	* @param array $patterns List of patterns, where keys are keys to be resurned and values are patterns for preg_match
	* @return array List of parsed data
	*/
	public function cmdPatterns($cmd, $patterns) {
		$data = $this->cmdShell($cmd);
		
		$ret = array();
		foreach($patterns as $key => $pattern) {
			if(preg_match($pattern, $data, $match)) $ret[$key] = sizeof($match) == 2 ? $match[1] : $match;
			else $ret[$key] = false;
		}
		
		return $ret;
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
	* Provedeme seznam prikazu, necekame na vysledek
	* @return null
	*/
	public function cmdListNoWait($commandList) {
		foreach($commandList as $k => $cmd) $this->shellCmd($cmd);
	}


	/**
	* Provede vypis
	* @param string $path Cesta, ktera se ma vypsat
	* @param string $podm Podminka pro parametr where
	* @param string $filtr Filtr pro print
	* @param string $more Dalsi parametry pro print
	*
	* Filtr je potreba napriklad pro protoze se pouziva /ip firewall filter print _all_ terse....
	*/
	public function export($path, $podm = null, $filtr = '', $more = null) {
		if(!$path) return false;

		// Spojime podminky do jedne
		if(is_array($podm)) {
			$podm2 = array();
			foreach($podm as $k => $v) {
				if(is_numeric($k)) $podm2[] = $v;
				else $podm2[] = "$k=$v";
			}
			$podm = implode($this->is3 ? ' and ' : ' ', $podm);
		}

		// Volame funkci podle verze mikrotika
		if($this->is3) {
			try {
				$ret = $this->export_mk3($path, $podm, $filtr, $more);
			} catch(Exception $e) { $ret = array(); }
		}
		else $ret = $this->export_mk2($path, $podm, $filtr, $more);

		return $this->_export = $ret;
	}

	/**
	* Vrati seznam polozek se zadanymi IDcky
	*/
	public function exportFrom($path, $ids) {
		// Volame funkci podle verze mikrotika
		if($this->is3) return $this->export_mk3($path, '', '', "from=$ids");
		else return $this->export_mk2_from($path, $ids);

	}

	/**
	* Export z mikrotika verze 3
	* viz fce export
	*/
	private function export_mk3($path, $podm = null, $filtr = '', $more = null) {
		if(empty($more) && empty($filtr)) {
			// Connect using routerOS
			if(empty($this->routeros)) {
				try {
					$this->routeros = new RouterOS($this->host, $this->user, $this->password);
				} catch(Exception $e) {
					// Enable api
					$this->ssh->execWait('/ip service enable api');
					
					// Try again
					$this->routeros = new RouterOS($this->host, $this->user, $this->password);
				}
			}
			return $this->routeros->find($path, $podm);
		}
		
		// DEPRECATED
		
		// Nacteme data
		$podm = $podm ? "where $podm" : '';
		$data = $this->ssh->execWait($cmd = ":put [/$path print $filtr as-value $more $podm ]");
		$data = trim($data);

		// Parsujeme vysledek
		return mikrotik_parse_asValue($data);
	}


	/**
	* Export z mikrotika verze 2
	* viz fce export
	*/
	public function export_mk2($path, $podm = null, $filtr = '', $more = null) {
		// Nacteni IDcek
		$ids = rtrim($this->ssh->shellCmdWait(":put [/$path find $podm]"));
		if(empty($ids) || startsWith($ids, 'no such')) return array();

		// Najdeme hodnoty
		return $this->export_mk2_from($path, $ids, $podm);
	}

	/**
	* Export hodnot z MK2 podle zadanych IDcek
	*/
	private function export_mk2_from($path, $ids, $podm = null) {
		// Vypis hodnot
		$podm2 = $podm ? "[/$path find $podm]" : $ids;
		$data = ($this->ssh->shellCmdWait("/$path export from=$podm2"));

		$param_list = mikrotik_parse_cmd($data);
		unset($param_list[0]);
		while(list($key) = each($param_list)) unset($param_list[$key][0]);

		$ret = array();

		$id_list = split('[;,]', $ids);


		// Mame vysledek a je platny
		if($param_list && (sizeof($id_list) == sizeof($param_list))) $ret = array_combine($id_list, $param_list);
		else {
			echo $data;
			print_r($param_list);
			throw new Exception("Neplatny export ($path, $podm2) z MK: nalezeno " . sizeof($id_list) . " indexu a " . sizeof($param_list) . " polozek");
		}

		$this->_export = $ret;
		return $ret;
	}

	// Import
	// OBSOLETE
	function import_prepare($path, $arr) {
		$cmd_list = array();
		$remove_list = array();
		$ex = $this->_export or $ex = $this->export($path);

		// Porovname s aktualnim exportem
		foreach($arr as $index => $row) {
			if(empty($row) && !is_array($row)) { // tato polozka je prazdna -> smazeme
				$remove_list[] = $index;

			} elseif(!isset($ex[$index])) { // Chybiv exportu ->pridame
				$cmd_list[] = mikrotik_make_cmd('add', $row);

			} else { // Upravime
				// Policka co se nemeni ztusime
				foreach($row as $k=>$v) {
					if(@$ex[$index][$k] == $v || substr($k, 0, 1)=='_') unset($row[$k]);
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
	* Import dat s porovnanim existujicich
	* @param string $path Cesta, ktera se ma exportovat (popripade znak '|' a podminka)
	* @param array $arr Pole s novymi udaji
	* @param bool $removeNoSet Zda se maji odstranit ty, ktere nejsou nastavene
	*
	* OBSOLETE: mel by se pouzivat import2
	*/
	function import($path, $arr, $removeNoSet = false) {
		// Provedeme export
		$ex = $this->export($path);


		$importItems = array();

		// Projdeme a porovnavame
		foreach($arr as $index => $item) {
			// Vyznamna pole
			if(!isset($item['_sig'])) {
				$importItems[] = $item;
				continue;
			}
			if(!is_array($sig = $item['_sig'])) $sig = explode(',', $sig);


			// Projdeme vsechna pole z exportu a zjistima zda na nektere sedi
			$match = false;
			foreach($ex as $index2 => $exItem) {
				$match2 = true;

				// Kontrolujeme vsecka vyznamna pole
				foreach($sig as $field) if(@$exItem[$field] != $item[$field]) {
					$match2 = false;
					break;
				}

				// Sedi na toto
				if($match2) {
					// Jiz sedelo drive na neco jineho
					if($match) $importItems[$index2] = null;
					else {
						$importItems[$index2] = $item;
						unset($ex[$index2]);
						$match = true;
					}
				}
			}

			// Nesedi na zadne
			if(!$match) $importItems[] = $item;
		}

		// Smazeme vsecky, co nebyly nastavene
		if($removeNoSet) foreach($ex as $index => $v) $importItems[$index] = null;


		// Najdeme zmeny
		$cmd_list = $this->import_prepare($path, $importItems);

		// Zadne zmeny
		if(empty($cmd_list)) return true;

		// Presun do slozky
		@list($path, $params) = explode('|', $path);
		array_unshift($cmd_list, "/$path");

		return $cmd_list;
	}

	/**
	* Import dat s porovnanim existujicich
	* @param string $path Cesta, ktera se ma exportovat
	* @param string $filter Filtr, ktery se ma pouzit (vecinou '' nebo 'all')
	* @param string $podm Podminka pro prikaz v MK
	* @param array $arr Pole s novymi udaji
	* @param bool $removeNoSet Zda se maji odstranit ty, ktere nejsou nastavene
	* @param array $info Vystupni informace
	*/
	function import2($path, $filter, $podm, $arr, $removeNoSet = false, &$info = null) {
		$info = array();

		// Zkusime najit parametry pro optimalizaci
		{
			// Spocitame pocet jednotlivych signatur
			$optimizeCount = array();
			foreach($arr as $item) {
				if(empty($item['_sig'])) continue;

				if(!is_array($sig = $item['_sig'])) $sig = explode(',', $sig);

				foreach($sig as $sigName) if($sigName) @$optimizeCount[$sigName]++;
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
		if(!is_array($ex = $this->export($path, $podm, $filter))) {
			echo "<pre><b>Nedostali jsme pole, ale $ex</b>"; var_dump($ex); throw new Exception("eee");
		}
/*	
		echo '<pre>';
		echo "Stare:"; print_r($ex);
		echo "Nove:"; print_r($arr);
		exit;
/**/		
		// echo "Polozky byly nacteny\n";

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
					elseif(!$val2) $search = call_user_func_array('array_merge_assoc', @$optimizedEx[$val1]);
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
							if(in_array($k, array('disabled', 'passthrough')) && !isset($exItem[$k])) unset($item[$k]);
							elseif(@$exItem[$k] == $v || substr($k, 0, 1)=='_') unset($item[$k]);
						}

						// Jsou nejake zmeny
						if($item) {
							$cmd_list[] = mikrotik_make_cmd("set $index2", $item);
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

	/**
	* Zkontroluje zda jiz neexistuje a prida
	*/
	function add($path, $sig, $values) {
		// Nachystame podminku
		$podm = array();
		foreach(is_array($sig) ? $sig : explode(',', $sig) as $field) {
			$podm[] = sprintf('%s="%s"', $field, $values[$field]);
		}
		$podm = implode(' ', $podm);

		// Zda uz neexistuje
		$ret = $this->cmd($cmd = ":put [/$path find $podm]");
		
		// Existuje -> zmenime
		if(preg_match('/^[*][0-9A-F]+$/i', $ret)) {
			// Prikaz pro zmenu
			$cmd = mikrotik_make_cmd("/$path set $ret", $values);
			return $this->cmd($cmd);
		}
		
		// Neexistuje -> pridame
		elseif(empty($ret)) {
			// Prikaz pro pridani
			$cmd = mikrotik_make_cmd("/$path add", $values);
			return $this->cmd($cmd);
		}
		
		else return $ret;
	}
	
	/**
	* Provede vypis
	* @param string $path Cesta, ktera se ma vypsat
	* @param string $more Dalsi parametry pro print
	* @param string $filtr Filtr pro print
	*
	* Filtr je potreba napriklad pro protoze se pouziva /ip firewall filter print _all_ terse....
	*/
	function seznam($path, $more = '', $filtr = '') {
		// Provedeme vypis
		$cmd = "/$path print $filtr terse without-paging $more";
		$this->debug("seznam '$path'");
		$this->debug("  executing $cmd");
		$data = $this->cmdShell($cmd);
		$this->debug("  execution done, got " . strlen($data) . ' bytes');
		
		// Parsujeme
		require_once('./scripts/mikrotik.inc.php');
		
		$this->debug('  parsing');
		$ret = mikrotik_parse_print($data);
		$this->debug('  parse done');
		return $ret;
	}

	/**
	* Vypis s podminkou
	* @param string $path Cesta v MK
	* @param string $podm Podminka pro parametr where
	*/
	function seznamWhere($path, $podm, $filtr = '') {
		// Je zadane jako pole parametru
		if(is_array($podm)) {
			$podm2 = array();
			foreach($podm as $k => $v) $podm2[] = sprintf('%s="%s"', $k, addslashes($v));
			
			$podm = implode(' and ', $podm2);
		}
		
		
		return $this->seznam($path, $podm ? "from=[/$path find $podm]" : '', $filtr);
		
		// Nefunguje ve 2.9
		return $this->seznam($path, $podm ? "where ($podm)" : '');
	}
}


/**
* Parsovani dat prijatych prikazem 'print as-value'
*/
function mikrotik_parse_asValue($data) {
	if(trim($data) === '') return array(); // Neni nic

	$ret = array();
	$list = explode(';', $data);

	$actualRow = null;
	$lastName = null;

	// Projdeme vsecky dvojice hodnot
	foreach($list as $polozka) {
		// Rozdelime na nazev a hodnotu
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

	return $ret;
}

