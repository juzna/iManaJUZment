<?
// Funkce pro praci s konfigurakama Winboxu

function winbox_load($data) {
	// Otestujeme hlavicku
	$header = "\x0F\x10\xC0\xBE";
	if(substr($data, 0, 4)!=$header) {
		user_error('Neplatny soubor');
		return false;
	}
		

	$data = substr($data, 4);
	$ret = array();
	
	for($index = $x = 0; $x < 1000 && $data; $x++) {
		// Prvni 2 bajty vyjadruji delku
		$len = ord($data{0}) + ord($data{1}) * 256;
		
		// Ziskame par atribut=hodnota
		$pair = substr($data, 2, $len);

		// Posuneme se na dalsi
		$data = substr($data, $len + 2);
		
		// Pokud je prazny, pokracujeme
		if(!$len) {
			$index++;
			continue;
		}

		
		// delka atrubutu
		$len2 = ord($pair{0});
		
		$name = substr($pair, 1, $len2);
		$value = substr($pair, 1 + $len2);	
		
		$ret[$index][$name] = $value;
	}

	return $ret;
}




function winbox_save($params) {
	// Hlavicka
	$header = "\x0F\x10\xC0\xBE";
	
	//Defaultni hodnoty
	$default =   array (
	    'type' => 'addr',
	    'host' => '',
	    'login' => 'admin',
	    'note' => '(neznamy)',
	    'secure-mode' => "\x01",
	    'keep-pwd' => "\x01",
	    'pwd' => '',
	);

	
	// Kontrola zda je prvni polozka informace o okne
	if(empty($params[0]['type'])) {
		$zaklad = array(
			'type' 		=> 'window',
			'bounds' 	=> pack('V*', 400, 47, 873, 900), // Pozice - left, top, right, bottom
			'list-col-0' => pack('V', 100),
			'list-col-1' => pack('V', 80),
			'list-col-2' => pack('V', 245),
		);
		array_unshift($params, $zaklad);
	}
	
	
	$data = $header;
	// Projdeme parametry
	foreach($params as $host) {
	
		if(isset($host['type'])) {
			// Nastaveni okna
			

		} else {
			// Co neni nastavime z defaultu
			foreach($default as $k=>$v) if(!isset($host[$k])) $host[$k] = $v;
		}
		
		// Ulozime parametry
		foreach($host as $name=>$value) {
			$pair = chr(strlen($name)) . $name . $value; // Vytvorime par
			$len = strlen($pair);
			
			$lenb = chr($len % 256) . chr($len / 256); // Delka binarne
			
			$data .= $lenb . $pair; // Pripojime
		}
		
		$data .= "\x00\x00"; // Ukonceni hostu
	}
	
	return $data;
}



function mikrotik_cmd($host, $port, $user, $password, $cmd_list, $trim = 0, $time_limit = 10, $delim = '') {	
	$time_start = microtime(true);
	
	// Pripojime se
	$mk = new mikrotikos($host, $port, $user, $password);
	
	// Provedeme prikazy
	$mk->allow_trimming = (bool) $trim;
	$ret = $mk->cmd($cmd_list, $delim);

	// Odpojime
	$mk->disconnect();
	
	return $ret;
}


// Davkovy prikaz pro MK
function mikrotik_davka($host, $port, $user, $password, &$cmd, $trim = 0, $time_limit = 10, $delim = '') {

	// Pripravime si prikazy
	$cmd = preg_replace('|^#.*$|m', '', $cmd); // Odstranime komentare
	$cmd = str_replace("\r", '', $cmd); // Smazeme \r
	$cmd_list = explode("\n\n", $cmd); // Rozdelime na jednotlive prikazy			
	
	
	// Vypis prikazu
	echo("<h2>Budu vykonavat prikazy:</h2>\n<table border=1>");
	foreach($cmd_list as $k=>$v) echo("<tr valign=top><td>$k <td>$v");
	echo('</table><h2>Vysledek</h2><pre>');
	
	
	// Vykoname
	$ret = mikrotik_cmd($host, $port, $user, $password, $cmd_list, $trim, $time_limit, $delim);
	return $ret;
}


// Nachysta prikaz pro MK
function mikrotik_make_cmd($command, $params = array()) {
	$ret = "$command ";
	
	foreach($params as $name => $value) {
		if(substr($name, 0, 1)=='_') continue;
		if(is_numeric($name)) $ret .= "$value ";
		else {
			$value = addslashes($value);
			$ret .= "$name=\"$value\" ";
		}
	}
	return $ret;
}

// Vezme davku prikazu
function mikrotik_parse_cmd_list($data) {
	// Rozpozname jednotlive prikazy
	$cmd_index = 0;
	$cmd_list = array();
	$lines = explode("\n", $data);
	foreach($lines as $line) {
		$line = trim($line);
		if(empty($line)) continue;
		if(substr($line, 0, 1)=='#') continue; // Komentar
		
		$is_continuous = (substr($line, -1) == '\\');
		if($is_continuous) $line = substr($line, 0, -1);
		
		@$cmd_list[$cmd_index] .= $line;
		if(!$is_continuous) $cmd_index++;
	}
	return $cmd_list;
}

// Rozparsuje prikazy pro MK
function mikrotik_parse_cmd($data) {
	$cmd_list = mikrotik_parse_cmd_list($data);
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

function mikrotik_parse_print($data) {
	$ret = array();
	
	// Odsazeni novych radku
	$data = str_replace("\n          ", '', $data);
	
	// Rozdelime na radky
	$rows = explode("\n", $data);
	
	// Prvni je radek s flagama
	$myFlags = null;
	if(startsWith($rows[0], 'Flags:')) {
		$row = array_shift($rows);
		
		// Prevedeme na asociativni pole
		// $myFlags = array('X' => 'disabled' ....)
		if(preg_match_all('/([A-Z]) - ([a-z]+)/', $row, $match)) $myFlags = array_combine($match[1], $match[2]);
	}
	
	// Projdeme jednotlive radky
	foreach($rows as $line) {
		if(startsWith($line, 'echo: ')) continue; // Nejaka blba hlaska
		
		$line .= "\n"; // Ukoncovaci znak
		
		$key = null;
		$isString = false;
		$isIndex = true;
		$paramName = $prev = $tmp = '';
		$flags = $params = array();
		
		$len = strlen($line);
		for($x = 0; $x < $len; $x++) { // Projdeme kazdy znak
			$znak = $line{$x};
			$slashed = (bool) ($prev == '\\');
			
			$save = true;
			
			if(($znak==' ' or $znak=="\r" or $znak=="\n") && !$isString) { // Konec parametru
				if($paramName) {
					if(!trim($tmp)) {
						$tmp .= $znak;
						continue;
					}
					
					// Ulozeni hodnoty
					if(!isset($params[$paramName])) $params[$paramName] = '';
					$params[$paramName] .= $tmp;
				} elseif(trim($tmp) !== '') {
					if($isIndex) {
						$key = $tmp;
						$isIndex = false;
					}
					
					else $flags[] = trim($tmp);
				}
				
				$tmp = $znak;
			}
			
			// Zadan nazev parametru
			elseif($znak == '=') {
				$paramName = ltrim($tmp);
				$tmp = '';
			}
			
			//Hranice stringu
			elseif($znak == '"' && !$slashed) {
				if($isString) {
					$isString = false;
				} else {
					$isString = true;
				}
			}
			
			//Backslash
			elseif($znak=='\\' && !$slashed) {
			}
			
			// Znak
			else {
				$tmp .= $znak;
			}
			
			$prev = $znak;
		}
		
		// Nastavime flagy
		if($myFlags) {
			// Existujici
			foreach($flags as $v) if(isset($myFlags[$v])) $params[$myFlags[$v]] = 'yes';
			
			// Implicitni
			foreach($myFlags as $v) if(!isset($params[$v])) $params[$v] = 'no';
		}
		
		if(is_null($key)) {
			echo "Klic neni nastaven\n" . $line;
			continue;
		}
		
		if(isset($ret[$key])) {
			echo "Klic $key uz existueje!\n" . $line;
			continue;
		}
		
		$ret[trim($key)] = $params;
	}
	
	return $ret;
}