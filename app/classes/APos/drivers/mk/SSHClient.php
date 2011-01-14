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

class SSHClient extends \SSHClient {
	public $version;
	public $major; // 2.9 or 3
	public $minor; // last number of version


  /**
   * Find version when we're connected
   * callback: when ssh client gets connected
   */
  public function connected() {
    $this->getVersion();
  }

  /**
   * Get SSH client from AP model
   * @throws Exception
   * @param AP $ap
   * @return Mikrotik\SSHClient
   */
  public static function fromAP(\AP $ap) {
    $client = new SSHClient($ap->getIP());
    $client->authPass($ap->username, $ap->pass);

    // Validate fingerprint
    $fing = $client->getFingerprint();
    if($ap->sshFingerprint) {
      if($fing != $ap->sshFingerprint) throw new \Exception('Fingerprint not matches');
    }
    else {
      $ap->sshFingerprint = $fing;
      $ap->flush();
    }

    return $client;
  }


  /**
   * Display part of configuration
   * @param string $path Path in MK to be printed
   * @param string $method
   * @return array
   */
  public function display($path, $method = null) {
    $path = $this->normalizePath($path);

    switch($method) {
      case 'print-terse':
      case null:
        return $this->unifyValues($this->printTerse($path));

      case 'print-as-value':
        return $this->unifyValues($this->printAsValue($path));

      case 'print-detail':
        return $this->unifyValues($this->printDetail($path));

      default:
        throw new \InvalidArgumentException("Unknown method");
    }
  }




  /**
   * Some print command in Mikrotik needs filter parameter, return a general one in these cases
   * @param string
   * @return string
   */
  protected function getFilter($path) {
    if($this->major != '2.9') throw \UnsupportedException("Filter is valid only for Mikrotik 2.9");

    switch($path) {
      case 'ip firewall mangle':	return 'all';
      case 'ip firewall nat':		return 'all';
      case 'ip firewall filter':	return 'all';
      default: return '';
    }
  }

  /**
   * Normalize path for exports
   * @param string
   * @return string
   */
  protected function normalizePath($path) {
    if(!is_array($path)) $path = preg_split('|[/ ]+|', $path);
    if(empty($path[0])) array_shift($path);
    return implode(' ', $path);
  }

  /**
   * Unify values returned by misc commands or misc versions of Mikrotik
   * @param array $arr
   * @return array
   */
  public function unifyValues($arr) {
    return RouterOS::unifyValues($arr, $this->version);
  }

  /**
   * Get Mikrotik's version
   * @return string
   */
  public function getVersion() {
    if(isset($this->version)) return $this->version;

		// Get version
		$version = $this->version = $this->execShellWait(':put [/system resource get version]');

		// Another try
		if(startsWith($version, 'input does not match')) {
			$version = $this->version = $this->execShellWait(':put [/system package get system version]');
		}

    // Parse it
    @list($major, $minor) = explode('.', $version, 2);
    if($major == 2) {
      list($major_, $minor) = explode('.', $minor, 2);
      $this->major = $major . '.' . $major_;
      $this->minor = $minor;
    }
    else {
      $this->major = $major;
      $this->minor = $minor;
    }

    return $version;
  }


  /*****************    Exporters   **************/

  public function printTerse($path, $from = null, $shell = true) {
    $cmd = "/$path print terse without-paging " . (isset($from) ? " from=$from " : '' ) ." ]";
    return $this->_parsePrintTerse($this->_execute($shell, $cmd));
  }

  public function printAsValue($path, $from = null, $shell = true) {
    if($this->major != 3) throw new \Exception("'print as-value' is not supported by MK $this->version");
    $cmd = ":put [/$path print as-value without-paging " . (isset($from) ? " from=$from " : '' ) ." ]";
    return $this->_parsePrintAsValue($this->_execute($shell, $cmd));
  }

  public function printDetail($path, $from = null, $shell = true) {
    $cmd = "/$path print detail without-paging " . (isset($from) ? " from=$from " : '' ) ." ]";
    return $this->_parsePrintDetail($this->_execute($shell, $cmd));
  }

  public function printExport($path, $from = null, $shell = true) {
    $cmd = "/$path export" . (isset($from) ? " from=$from " : '' ) ." ]";
    return $this->_parseExport($this->_execute($shell, $cmd));
  }

  public function _execute($useShell, $cmd) {
    if($useShell) return $this->execShellWait($cmd);
    else return $this->execWait($cmd);
  }


  /***********    Parsers     *************/

  public function _parsePrintTerse($data) {
    throw new \Exception("Not implemented yet");
  }

  public function _parsePrintAsValue($data, $ids = null) {
		if(trim($data) === '') return array(); // No data present

		// There's an error
		if(String::startsWith($data, 'input does not match')) throw new \InvalidStateException("Mikrotik says: $data");

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
		$idList = preg_split('/[;,]/', $ids);

		// Combine ids and params and return
		if($ret && (sizeof($idList) == sizeof($ret))) return array_combine($idList, $ret);
		else throw new \InvalidStateException("Invalid export commands: got " . sizeof($idList) . " indexes and " . sizeof($ret) . " items");
  }


  public function _parsePrintDetail($data, $ids = null) {
    if(trim($data) === '') return array(); // No data present

    // There's an error
    if(String::startsWith($data, 'input does not match')) throw new \InvalidStateException("Mikrotik says: $data");
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
		$idList = preg_split('/[;,]/', $ids);

		// Combine ids and params and return
		if($ret && (sizeof($idList) == sizeof($ret))) return array_combine($idList, $ret);
		else throw new \InvalidStateException("Invalid export commands: got " . sizeof($idList) . " indexes and " . sizeof($ret) . " items");
  }

  public function _parseExport($data, $ids = null) {
    $ret = Commands::parseList($data);

    // Remove some special values
    unset($ret[0]);
    while(list($key) = each($ret)) unset($ret[$key][0]);

    if(is_null($ids)) return $ret;

		// List of ID's
		$idList = preg_split('/[;,]/', $ids);

		// Combine ids and params and return
		if($ret && (sizeof($idList) == sizeof($ret))) return array_combine($idList, $ret);
		else throw new \InvalidStateException("Invalid export commands: got " . sizeof($idList) . " indexes and " . sizeof($ret) . " items");    
  }

}
