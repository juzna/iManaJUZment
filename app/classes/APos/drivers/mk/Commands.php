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
 * Work with command of Mikrotik console
 */
class Commands {
  /**
   * Prepare command for Mikrotik shell
   * @param string $command Base part of command
   * @param array $params List of parameters
   * @return string
   */
  public static function make($command, $params = array()) {
    $ret = "$command ";
	
    foreach($params as $name => $value) {
      if(substr($name, 0, 1) == '_' || substr($name, 0, 1) == '.') continue;
      if(is_numeric($name)) $ret .= "$value ";
      else {
        $value = addslashes($value);
        $ret .= "$name=\"$value\" ";
      }
    }
    return $ret;
  }
  
  /**
   * Parse list of command in big buffer and return array of commands
   * @param string $data
   * @return array
   */
  public static function splitList($data) {
    $cmd_index = 0;
    $cmd_list = array();
    $lines = explode("\n", $data);
    foreach($lines as $line) {
      $line = trim($line);
      if(empty($line)) continue;
      if(substr($line, 0, 1)=='#') continue; // Comment, skip it
      
      $is_continuous = (substr($line, -1) == '\\');
      if($is_continuous) $line = substr($line, 0, -1);
      
      @$cmd_list[$cmd_index] .= $line;
      if(!$is_continuous) $cmd_index++;
    }
    return $cmd_list;    
  }
  
  /**
   * Parse one command
   * @param string $data
   * @return array
   */
  public static function parseOne($line) {
		$line .= "\n"; // Ukoncovaci znak

		$is_command = false;
		$is_string = false;
		$command = $param_name = $prev = $tmp = '';
		$params = $m = $m2 = array();
		
		$len = strlen($line);
		for($x = 0; $x < $len; $x++) { // Projdeme kazdy znak
			$char = $line{$x};
			$slashed = (bool) ($prev=='\\');
			
			$save = true;
			
			if(($char==' ' or $char=="\r" or $char=="\n") && !$is_string) { // Konec parametru
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
				
			} elseif($char=='=') { // Zadan nazev parametru
				$param_name = $tmp;
				$tmp = '';
				
			} elseif($char=='"' && !$slashed) { //Hranice stringu
				if($is_string) {
					$is_string = false;	
				} else {
					$is_string = true;
				}
			
			} elseif($char=='\\' && !$slashed) { //Backslash
				$save = false;
			
			} else { // Znak
				$tmp .= $char;
				$save = false;
			}

			$prev = $char;
		}
		
		return $params;
  }

  /**
   * Parse multiple commands
   * @param string $data
   * @return array of arrays
   */
  public static function parseList($data) {
    return array_map(array(__CLASS__, 'parseOne'), self::splitList($data));
  }
  
  public static function parsePrint($data) {
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
        $char = $line{$x};
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
          } elseif(trim($tmp) !== '') {
            if($isIndex) {
              $key = $tmp;
              $isIndex = false;
            }

            else $flags[] = trim($tmp);
          }

          $tmp = $char;
        }

        // Zadan nazev parametru
        elseif($char == '=') {
          $paramName = ltrim($tmp);
          $tmp = '';
        }

        //Hranice stringu
        elseif($char == '"' && !$slashed) {
          if($isString) {
            $isString = false;
          } else {
            $isString = true;
          }
        }

        //Backslash
        elseif($char=='\\' && !$slashed) {
        }

        // Znak
        else {
          $tmp .= $char;
        }

        $prev = $char;
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
}
