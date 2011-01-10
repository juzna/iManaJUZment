<?php

namespace Mikrotik;

/**
* RouterOS API for Mikrotik v3
* @author Jan Dolecek - juzna.cz
*/
class RouterOS {
	public $debug = false;		// Show debug information
	
	private $ip;
	private $username;
	private $password;
	private $error_no;		// Variable for storing connection error number, if any
	private $error_str;		// Variable for storing connection error text, if any
	private $connected = false;	// Connection state
	private $socket;		// Socket storage
	private $port = 8728;		// Port to connect to
	private $writeCounter = 0;	// Tag counter of writen commands
	private $readBuffer;		// Buffer for received, but not readed data
	private $arrBuffer;		// Buffer for received array items
	private $arrBufferMetadata;	// Buffer for received metadata while receiving array
	private $callbacks;		// Store callbacks
	private $delay = 3;		// Delay between connection attempts in seconds
	private $timeout = 3;		// Connection attempt timeout and data read timeout
	public $version;		// Version of MK
	public $lastMetadata;
	private $doneResult = array();
	public $lastDoneResult;
	
	// Keys which have boolean values
	public static $booleanKeys = array(
		'dynamic', 'static', 'invalid', 'active', 'enabled', 'disabled', 'passthru', 'running', 'slave', 'radius', 'blocked',
		'connect', 'rip', 'bgp', 'ospf', 'mme', 'blackhole', 'unreachable', 'prohibit'
	);
	
	/**
	* Constructor
	* @param string $ip RouterOS ip address to connect to
	* @param string $username Login username
	* @param string $password Login password
	* @throw RouterOSException when unable to connect
	*/
	public function __construct($ip = null, $username = null, $password = null, $version = null) {
		if($ip) {
			// Try to connect
			$ret = $this->connect($ip, $username, $password);
			
			if(!$ret) throw new RouterOSException("Unable to connect or login");
		}
		if(!$this->version = $version) $this->getVersion();
	}
	
	public function __destruct() {
		$this->disconnect();
	}
	
	public function getVersion() {
		$ret = $this->getall('system resource');
		if(!$ret) return null;
		return $this->version = $ret[0]['version'];
	}
	
	/**
	* Write command and asynchronously observes and executes given callback function
	* @param string $command Command name
	* @param array $args List of arguments
	* @param callable $callback Callback function
	* @param mixed $callbackParams Parameters for callback function
	* @return string Tag of this command
	*/
	public function observe($command, $args, $callback, $callbackParams = null) {
		// Write command
		$tag = $this->write($command, $args);
		
		// Store callback
		$this->callbacks[$tag] = array($callback, $callbackParams, $command);
		
		return $tag;
	}
	
	/**
	* Sentence for given callback is read -> execute callback
	* @param string $tag Tag of executed command
	* @param array $sentence Read sentence
	*/
	private function raise($tag, $sentence) {
		$callback = $this->callbacks[$tag][0];
		$params   = $this->callbacks[$tag][1];
		$command  = $this->callbacks[$tag][2];
		
		$params[] = $sentence;
		$params[] = $command;
		$params[] = $tag;
		
		// Execute callback
		call_user_func_array($callback, $params);
	}
	
	/**
	* Cancels action and remove event observed by calling observe method
	* @see observe
	* @param string $tag Tag of executed command
	*/
	public function cancel($tag) {
		// Write tag command
		$this->write('/cancel', array('tag' => $tag));
		
		$this->clear($tag);
	}
	
	/**
	* Removes callback from memmory
	*/
	public function clear($tag) {
		// Remove callback
		unset($this->callbacks[$tag]);
	}
	
	/**
	* Process loop - keep reading data and execute callbacks
	*/
	public function loop($time = 60) {
		// Set time limit
		if($time == 0) $waitUpTo = false;
		else $waitUpTo = time() + $time;
		
		// Main cycle
		while($waitUpTo === false || $waitUpTo > time()) {
			// Wait for some data on socket
			$selectTime = ($waitUpTo === false) ? 30 : min($waitUpTo - time(), 30);
			$read = array($this->socket);
			stream_select($read, $_a = null, $_b = null, $selectTime);
			
			// Read to buffer
			$this->readToBuffer();
		}
	}
	
	/**
	* Write a command and return parsed output
	*/
	public function request($command, $args = null, $timeout = 5, &$metaData = null) {
		// Write command
		$tag = $this->write($command, $args);
		
		// Read array
		$this->arrBuffer[$tag] = array(); // Prepare empty array
		$ret = $this->readArr($tag, $timeout);
		
		// Save metadata
		$this->lastMetadata = $metaData = @$this->arrBufferMetadata[$tag];
		$this->lastDoneResult = @$this->doneResult[$tag]['ret'];
		
		return $ret;
	}
	
	/**
	* Execute arbitrary command
	*/
	public function execute($path, $cmd, $args, $timeout = 5) {
		// Write command
		$tag = $this->write($this->makeCommand($path, $cmd), $args);
		
		// Read array
		$this->arrBuffer[$tag] = array(); // Prepare empty array
		$retData = $this->readArr($tag, $timeout);
		
		// Save metadata
		$this->lastMetadata = @$this->arrBufferMetadata[$tag];
		
		// Get result
		$retDone = @$this->doneResult[$tag]['ret'];
		unset($this->doneResult[$tag]);
		
		return array($retData, $retData);
	}
	
	/**
	* Add new item to MK
	* @return string ID of new item (property .id)
	*/
	public function add($path, $args, $timeout = 5) {
		// Write command
		$tag = $this->write($this->makeCommand($path, 'add'), $args);
		
		// Read array
		$this->arrBuffer[$tag] = array(); // Prepare empty array
		$this->readArr($tag, $timeout);
		
		// Save metadata
		$this->lastMetadata = @$this->arrBufferMetadata[$tag];
		
		// Get result
		$ret = $this->doneResult[$tag]['ret'];
		unset($this->doneResult[$tag]);
		
		return $ret;
	}
	
	/**
	* Get all items in path
	* Uses getall API from MK
	* @param string $path Path delimited by spaces or slashes
	* @param string $proplist List of properties to get (see MK API)
	* @param int $timeout Timeout in seconds to wait
	* @return array Array of items
	*/
	public function getall($path, $proplist = null, $timeout = 25) {
		// Property list for MK that supports it
		if($this->version >= '3.21' && $proplist) {
			if(!in_array('.id', explode(',', $proplist))) $proplist = ".id,$proplist";
			$args = array('.proplist' => $proplist);
		}
		else $args = array();
		
		// Send request
		$ret = $this->request($this->makeCommand($path, 'print'), $args, $timeout, $metaData);
		if(!isset($ret)) return null;
		
		// No such command exception
		if($metaData && $metaData[0][0] == '!trap' && strpos($metaData[0]['message'], 'no such command') !== false) throw new RouterOSException($metaData[0]['message']);
		
		return self::unifyValues($ret, $this->version);
	}
	
	/**
	* Make command of path and action
	*/
	public function makeCommand($path, $action) {
		if(!is_array($path)) $path = split('[/ ]', $path);
		
		// Remove first empty item
		if(empty($path[0])) array_shift($path);
		
		$path = implode('/', $path);
		return "/$path/$action";
	}
	
	/**
	* Universal search function
	*/
	public function search($path, $conditions, $proplist = null, $timeout = 10) {
		if($this->version < '3.21') return $this->find($path, $conditions, $proplist, $timeout);
		else return $this->query($path, $conditions, $proplist, $timeout);
	}
	
	/**
	* Find items using MK API Queries (@see http://wiki.mikrotik.com/wiki/API#Queries)
	* @param string $path Path to export
	* @param array $conditions List of conditions without leading question mark (eg. [ "name=x", "name2" ])
	* @param string $proplist List of properties to get (see MK API)	
	* @param int $timeout Timeout in seconds to wait	
	* @return array List of items
	*/
	public function query($path, $conditions, $proplist = null, $timeout = 10) {
		if($this->version < '3.21') throw new \UnsupportedException("Unsupported operation in this version ($this->version) of Mikrotik, please upgrade at least to 3.21");
		
		if(!is_array($path)) $path = preg_split('|[/ ]|', $path);
		
		// Remove first empty item
		if(empty($path[0])) array_shift($path);
		
		// Conditions given as string -> parse it
		if(is_string($conditions)) {
			$str = $conditions;
			$conditions = array();
			foreach(explode(' ', $str) as $cond) {
				if(preg_match('/^([*]?[a-z0-9-_]+)(=|!=|<|>|>=|<=)(.*)$/', $cond, $match)) {
					@list(, $key, $op, $val) = $match;
					
					// Unify value
					if(in_array($key, self::$booleanKeys) && in_array($val, array('no', 'false', 'yes', 'true'))) {
						$val = ($val == 'no' || $val == 'false') ? 'false' : 'true';
					}
					
					switch($op) {
						case '=': $conditions[] = "$key=$val"; break;
						default:
							throw new RouterOSException("Unsupported operator $op");
					}
				}
				else throw new RouterOSException("Unknown condition '$cond' - cannot be parsed");
			}
		}
		
		$args = array();
		if($proplist) {
			if(!in_array('.id', explode(',', $proplist))) $proplist = ".id,$proplist";
			$args['.proplist'] = $proplist;
		}
		if($conditions) foreach($conditions as $k => $v) {
			if(is_numeric($k)) $args[] = "?$v";
			else {
				// Unify value
				if(in_array($k, self::$booleanKeys) && in_array($v, array('no', 'false', 'yes', 'true'))) {
					$v = ($v == 'no' || $v == 'false') ? 'false' : 'true';
				}
				
				$args[] = "?$k=$v";
			}
		}
		
		// Send request
		$path = implode('/', $path);
		$ret = $this->request("/$path/print", $args, $timeout, $metaData);
		
		// No such command exception
		if($metaData && $metaData[0][0] == '!trap' && strpos($metaData[0]['message'], 'no such command') !== false) throw new RouterOSException($metaData[0]['message']);
		
		return self::unifyValues($ret, $this->version);
	}
	
	/**
	* Find items in path that match given pattern
	* Work for MK version older than 3.21 where queries were added (see query method for queries)
	* @param string $path Path to export
	* @param array $pattern List of conditions in form of array: [ name, operator, value ]
	* @param string $proplist List of properties to get (see MK API)
	* @param int $timeout Timeout in seconds to wait
	* @return array List of exported items
	*/
	public function find($path, $pattern, $proplist = null, $timeout = 10) {
		// No conditions -> get whole list
		if(empty($pattern)) return $this->getall($path, $proplist, $timeout);
		
		// Pattern given as string -> parse it
		if(is_string($pattern)) {
			$str = $pattern;
			$pattern = array();
			foreach(explode(' ', $str) as $cond) {
				if(preg_match('/^([*]?[a-z0-9-_]+)(=|!=|<|>|>=|<=)(.*)$/', $cond, $match)) {
					$pattern[] = array($match[1], $match[2], $match[3]);
				}
				else throw new RouterOSException("Unknown condition '$cond' - cannot be parsed");
			}
		}
		
		$ret = array();
		
		// Check all items
		$data = $this->getall($path, $proplist, $timeout);
		foreach($data as $item) {
			// Try parrern values
			foreach($pattern as $_val) {
				list($key, $op, $val) = $_val;
				
				// Unify value
				if(in_array($key, self::$booleanKeys) && in_array($val, array('no', 'false', 'yes', 'true'))) {
					$val = ($val == 'no' || $val == 'false') ? 'false' : 'true';
				}
				
				//echo @"Kontrola $key$op$val, original is {$item[$key]}\n";
				
				switch($op) {
					case '=':
						if(!isset($item[$key]) || $item[$key] != $val) continue 3; // Continue with next item
						break;
					
					case '!=':
						if(isset($item[$key]) && $item[$key] == $val) continue 3; // Continue with next item
						break;
						
					case '<':
						if(!isset($item[$key]) || $item[$key] >= $val) continue 3; // Continue with next item
						break;
						
					case '<=':
						if(!isset($item[$key]) || $item[$key] > $val) continue 3; // Continue with next item
						break;
						
					case '>':
						if(!isset($item[$key]) || $item[$key] <= $val) continue 3; // Continue with next item
						break;
						
					case '>=':
						if(!isset($item[$key]) || $item[$key] < $val) continue 3; // Continue with next item
						break;
						
					default:
						throw new RouterOSException("Unsupported operator '$op'");
				}
			}
			
			$ret[] = $item;
		}
		
		return $ret;
	}
	
	/**
	* Try to replace values different in some versions (eg. true/yes)\
	* @param string $value Value to be written to MK
	* @param string $key Name of that value
	* @param string $version Mikrotik version
	*/
	public static function tryToReplaceValue(&$value, $key, $version) {
		// It's not bool
		if(!in_array($key, self::$booleanKeys)) {
			if(in_array($value, array('yes', 'no', 'true', 'false'))) {
				// But it seems like bool
			
			}
		}
		
		// It's a bool
		else {
			if($value == 'no' || $value == 'false') $value = ($version >= '3.21') ? 'false': 'no';
			elseif($value == 'yes' || $value == 'true') $value = ($version >= '3.21') ? 'true': 'yes';
		}
	}
	
	/**
	* Unify values from different versions of MK
	* @param array $list List of exported items
	* @param string $version Version of Mikrotik
	*/
	public static function unifyValues(&$list, $version = null) {
		// Loop thru all items
		reset($list);
		while(list($index, $row) = each($list)) {
			foreach($row as $key => $val) {
				// Unify value
				if(in_array($key, self::$booleanKeys) && in_array($val, array('no', 'false', 'yes', 'true'))) {
					$list[$index][$key] = ($val == 'no' || $val == 'false') ? 'false' : 'true';
				}
			}
		}
		
		return $list;
	}
	
	/**
	* Write command and arguments to socket
	* @param string $command Command to be sent
	* @param array $args Command's arguments, if any; could be associative array in most cases
	* @return int Tag ID (unique sentence identifier)
	*/
	public function write($command, $args = null) {
		if(!$this->connected) throw new RouterOSException("Not connected");
		if(substr($command, 0, 1) != '/') throw new RouterOSException("Command must begin with /");
		
		// Prepare sentence
		$tag = ++$this->writeCounter; // Get new tag
		$words = array($command);
		$words[] = ".tag=$tag";
		if(is_array($args)) foreach($args as $k => $v) {
			if(is_numeric($k)) {
				if(substr($v, 0, 1) == '?') $words[] = $v;
				else $words[] = "=$v";
			}
			else {
				if($v === true) $words[] = "=$k=";
				else $words[] = "=$k=$v";
			}
		}
		
		// Write sentence
		$this->writeSentenceArr($words);
		
		return $tag;
	}
	
	/**
	* Read tagged data
	* @param int $tag Find data with this tag
	* @param int $wait Number of second to wait for data; when 0, no waits
	*/
	public function read($tag, $wait = 5) {
		if(!$this->connected) throw new RouterOSException("Not connected");
		
		// Process all received data and store to buffer
		$this->readToBuffer();
		
		// Missing data and we have to wait
		if(empty($this->readBuffer[$tag]) && $wait) {
			$waitUpTo = time() + $wait;
			
			do {
				usleep(10000); // 10 ms
				$this->readToBuffer();
			} while($waitUpTo > time() && empty($this->readBuffer[$tag]));
			
			// Still not received
			if(empty($this->readBuffer[$tag])) return null;
		}
		
		// No data
		if(empty($this->readBuffer[$tag])) return null;
		
		// Return first sentence
		return array_shift($this->readBuffer[$tag]);
	}
	
	/**
	* Read array of sentences
	*/
	public function readArr($tag, $wait = 5) {
		$waitUpTo = time() + $wait;
		
		// Still wait
		while($waitUpTo > time()) {
			// Read a sentence
			$words = $this->read($tag, $waitUpTo - time());
			
			// Array item
			if($words[0] == '!re') {
				// Store to buffer
				unset($words[0]);
				$this->arrBuffer[$tag][] = $words;
			}
			
			// We got it
			elseif($words[0] == '!done') {
				// Reference to buffer
				$ret = &$this->arrBuffer[$tag];
				
				// Save this result (may conain some data)
				$this->doneResult[$tag] = $words;
				
				// Delete buffer
				unset($this->arrBuffer[$tag]);
				
				// Return data
				return $ret;
			}
			
			// Store metadata
			else $this->arrBufferMetadata[$tag][] = $words;
		}
	}
	
	/**
	* Get metadata
	*/
	public function readMetaData($tag) {
		if(!empty($this->arrBufferMetadata[$tag])) {
			$ret = &$this->arrBufferMetadata[$tag];
			unset($this->arrBufferMetadata[$tag]);
			return $ret;
		}
	}
	
	/**
	* Read data from socket and store to buffer
	*/
	public function readToBuffer() {
		// Read incoming sencentes while available
		$limit = 100;
		while($limit-- > 0) {
			// Check whether we have data to be read from socket
			$read = array($this->socket);
			stream_select($read, $_a = null, $_b = null, 0);
		
			if($read) {
				$tag = null;
				$words = $this->readSentence();
				$ret = array( array_shift($words) ); // Reply name
				
				// Parse words
				while($words) {
					$word = array_shift($words);
					
					if(substr($word, 0, 5) == '.tag=') $tag = (int) substr($word, 5);
					else {
						$x = explode('=', $word, 3);
						
						if(!isset($x[1])) {
							var_dump($x);
							var_dump($word);
							var_dump($words);
						}
						//\Assert::condition(isset($x[1]), "Neznama data");
						
						$ret[$x[1]] = isset($x[2]) ? $x[2] : true;
					}
				}
				
				// Store to buffer
				if($tag) {
					// It has it's own handler -> raise event
					if(isset($this->callbacks[$tag])) $this->raise($tag, $ret);
					
					// Store to buffer
					else $this->readBuffer[$tag][] = $ret;
				}
				
				// Unknown
				else {
					$this->debug("Got untagged: " . print_r($ret, true));
				}
			}
			
			else break;
		}
	}
	
	/**
	* Debug information
	*/
	function debug($text) {
		if($this->debug) echo "RouterOS debug: $text\n";
	}
	
	/**
	* Read raw word
	*/
	private function readWord() {
		// Determine length of word
		{
			$byte1 = ord(fread($this->socket, 1));
			
			if(($byte1 & 0x80) == 0) { // Bits begin with 0 -> one byte
				$length = $byte1;
			}
			
			elseif(($byte1 & 0x80) == 0x80) { // Bits begin with 10 - two bytes
				$byte2 = ord(fread($this->socket, 1));
				$length = ($byte1 & 0x7f) << 8 | $byte2;
			}
			
			elseif(($byte1 & 0xc0) == 0xc0) { // Bits begin with 110
				$byte2 = ord(fread($this->socket, 1));
				$byte3 = ord(fread($this->socket, 1));
				
				$length = ($byte1 & 0x3f) << 16 | $byte2 << 8 | $byte3;
			}
			
			elseif(($byte1 & 0xe0) == 0xe0) { // Bits begin with 1110
				$byte2 = ord(fread($this->socket, 1));
				$byte3 = ord(fread($this->socket, 1));
				$byte4 = ord(fread($this->socket, 1));
				
				$length = ($byte1 & 0x1f) << 24 | $byte2 << 16 | $byte3 << 8 | $byte4;
			}
			
			elseif($byte1 == 0xf0) { // Bits begin is 11110000
				$byte1 = ord(fread($this->socket, 1));
				$byte2 = ord(fread($this->socket, 1));
				$byte3 = ord(fread($this->socket, 1));
				$byte4 = ord(fread($this->socket, 1));
				
				$length = $byte1 << 24 | $byte2 << 16 | $byte3 << 8 | $byte4;
			}
		}
		
		// Read data from socket
		if($length == 0) $ret = null;
		else $ret = fread($this->socket, $length);
		
		return $ret;
	}
	
	/**
	* Reads sentence from socket
	* @return array Array of words
	*/
	public function readSentence() {
		$ret = array();
		while(($word = $this->readWord()) !== null) $ret[] = $word;
		
		$this->debug("Read sentence: " . implode(' ', $ret));
		
		return $ret;
	}
	
	/**
	* Encode length (int to API's format)
	*/
	public static function lengthEncode($length) {
		if($length < 0x80) return chr($length);
		elseif($length < 0x4000) {
			$length |= 0x8000;
			return chr( ($length >> 8) & 0xFF) . chr($length & 0xFF);
		}
		elseif($length < 0x200000) {
			$length |= 0xC00000;
			return chr( ($length >> 16) & 0xFF) . chr( ($length >> 8) & 0xFF) . chr($length & 0xFF);
		}
		elseif($length < 0x10000000) {
			$length |= 0xE0000000;
			return chr( ($length >> 32) & 0xFF) . chr( ($length >> 24) & 0xFF) . chr( ($length >> 16) & 0xFF) . chr($length & 0xFF);
		}
		elseif($length >= 0x10000000) {
			return chr(0xF0) . chr( ($length >> 32) & 0xFF) . chr( ($length >> 24) & 0xFF) . chr( ($length >> 16) & 0xFF) . chr($length & 0xFF);
		}
	}
	
	/**
	* Write sentence to socket
	* Pass all parameters to writeSentenceArr
	*/
	public function writeSentence() {
		$arr = func_get_args();
		return $this->writeSentenceArr($arr);
	}
	
	/**
	* Write sentence to socket
	* @param array $words Array of words
	*/
	public function writeSentenceArr($words) {
		$this->debug("Writing sentence: " . implode(' ', $words));
		
		foreach($words as $word) $this->writeWord($word);
		$this->writeWord(null); // Zero word ends the sentence
	}
	
	/**
	* Write word to socket
	*/
	private function writeWord($word) {
		$len = strlen($word);
		
		if($len) fwrite($this->socket, self::lengthEncode($len) . $word);
		else fwrite($this->socket, chr(0));
	}
	
	/**
	* Connect to RouterOS
	* @param string $ip IP address of router
	* @param string $username
	* @param string $password
	* @param int $attempts Number of attempts to retry
	* @return bool True if success, false otherwise
	*/
	public function connect($ip, $username, $password, $attempts = 3) {
		if($this->connected) return true; // Already connected
		
		// Store login info
		$this->ip = $ip;
		$this->username = $username;
		$this->password = $this->password;
		
		// Try it
		while($attempts-- > 0) {
			
			// Connected
			if($this->socket = @fsockopen($ip, $this->port, $this->error_no, $this->error_str, $this->timeout)) {
				stream_set_blocking($this->socket, true);
				// socket_set_timeout($this->socket, $this->timeout);
				
				// Write login
				$this->writeSentence('/login');
				
				// Read challenge
				$ret = $this->readSentence();
				
				// Expected done and challenge
				if(sizeof($ret) == 2 && $ret[0] == '!done' && preg_match('/^=ret=([0-9a-f]+)$/', $ret[1], $match)) {
					$challenge = $match[1];
				} else {
					fclose($this->socket);
					return false;
				}
				
				// Create password hash
				$hash = md5(chr(0) . $password . pack('H*', $challenge));
				
				// Send login data
				$this->writeSentence('/login', "=name=$username", "=response=00$hash");
				
				
				// Read result
				$ret = $this->readSentence();
				if(sizeof($ret) >= 1 && $ret[0] == '!done') {
					$this->connected = true;
					return true;
				}
				
				// Auth failed
				else {
					fclose($this->socket);
					return false;
				}
			}
			
			// Socket opening failed
			else {
				// Sleep and try again
				if($attempts > 0) sleep($this->delay);
			}
		}
		
		// Fail
		return false;
	}
	
	/**
	* Disconnect routeros
	*/
	public function disconnect() {
		if($this->connected) {
			fclose($this->socket);
			$this->connected = false;
		}
	}
	
	/**
	* Get socket (so that user is able to call select on it)
	*/
	public function getSocket() {
		return $this->socket;
	}
}


class RouterOSException extends \Exception {}
