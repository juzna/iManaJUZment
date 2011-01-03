<?php
/**
* Simple web browser
* @package Browser
*/


class Browser {
	/**
	* Zda jsou povoleny debug hlasky
	*/
	public static $allowDebug = false;

	/**
	* Pocitadlo browseru ve zpracovani stranky
	*/
	private static $counter = 0;

	/**
	* Aktualni index pocitadla pro tento browser
	*/
	private $myCounter;

	public $id = null;

	public $lastUrl = null;

	/**
	* Kolacky
	*/
	public $cookies = array();

	/**
	* Options pro CURL
	*/
	public $options = array(
		CURLOPT_FOLLOWLOCATION	=> 0,
		CURLOPT_RETURNTRANSFER	=> 1,
		CURLOPT_TIMEOUT		=> 5,
		CURLOPT_HEADER		=> 1,
		CURLOPT_SSL_VERIFYPEER	=> false,
	);

	/**
	* Nejake moje parametry
	*/
	public $myOptions = array(
		'postSimple'	=> true,
	);

	/**
	* Hlavicky pro request
	*/
	public $requestHeaders = array();


	public $responseHeader = null;

	/**
	* Vystupni hlavicky
	*/
	public $responseHeaders = null;

	/**
	* Nazev serveru, ktery se ma nacitat
	*/
	public $server;

	// Chyby
	public $error = null;
	public $errno = null;

	public $data;


	public function __construct($id = null) {
		// Pocitadlo browseru
		$this->myCounter = self::$counter++;

		// Jednoznacne pojmenovani
		$this->id = $id;

		// Je zadano ID -> obnovime sezeni
		if($id) $this->restoreSession();
	}

	public function redirect($url, &$vysledek = null) {
		// Zda je to relativni adresa
		if(!preg_match('|^[a-z]+:[/][/][^/]+|', $url, $m)) {
			// Relativne vzhledem k serveru
			if(substr($url, 0, 1) == '/') {
				preg_match('|^[a-z]+:[/][/][^/]+|', $this->lastUrl, $m);
				$url = $m[0] . $url;
			}

			// Relativne vzhledem ke slozce
			else {
				$pos = strrpos($this->lastUrl, '/');
				$folder = substr($this->lastUrl, 0, $pos + 1);

				$url = $folder . $url;
			}
		}

		// Request
		return $this->request($vysledek = $url);
	}

	public function request($url, $post = null) {
		$this->debug("Nacitam stranku '$url'");

		// Nastavi referera
		if(@$this->myOptions['referer'] && $this->lastUrl) $this->setHeader('Referer', $this->lastUrl, true);

		// Posledni odkaz
		$this->lastUrl = $url;

//		echo "<b>Nacitam stranku $url\n</b>";

		// Inicializujeme
		$curl = curl_init($url);

		// Nastavime POST
		if($post) {
			curl_setopt($curl, CURLOPT_POST, 1); // Post metoda

			// Prevod na string, nebude to multipart
			if(is_array($post)) {
				if(numericKeys($post)) $post = implode('&', $post);
				elseif(isset($this->myOptions['postSimple'])) $post = http_build_query($post);
			}

			curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
		}

		// Stranka v ramci serveru
		$ex = explode('/', $url, 4);
		$page = '/' . $ex[3];

		// Nastavime hlavicky
		if($this->requestHeaders) curl_setopt($curl, CURLOPT_HTTPHEADER, $this->requestHeaders);
		//print_r($this->requestHeaders);
		if($c = $this->getCookieString($page)) {
			curl_setopt($curl, CURLOPT_COOKIE, $c);
			$this->debug("Nastavuju cookies na $c");
		}


		// Optionsy
		curl_setopt_array($curl, $this->options);

		// Zpracujeme
		$ret = curl_exec($curl);
		$this->errno = curl_errno($curl);
		$this->error = curl_error($curl);
		curl_close($curl);

		// Chyba :(
		if($ret === false) return false;

		// Rozdelime
		$pos = strpos($ret, "\r\n\r\n") or $pos = strpos($ret, "\n\n");
		if($pos) {
			$header = rtrim(substr($ret, 0, $pos));
			$data = ltrim(substr($ret, $pos));
		} else {
			$header = '';
			$data = $ret;
		}
		$this->data = $data;

		// Hlavicky
		$this->responseHeaders = explode("\n", $this->responseHeader = $header);

		// Nastavime prijate kolacky
		$this->autosetCookies();

		// Ulozeni
		if(@$this->myOptions['autosave']) $this->saveSession();

		// Zjistime kodovani
		if(!isset($this->myOptions['keepEncoding']) && preg_match('/charset=([a-z0-9_-]+)/i', $header, $match)) {
			// Prevod na cp1250
			$data = iconv($match[1], 'CP1250', $data);
		}

		return $data;
	}
	
	public function getState(&$text = null) {
		list($firstRow) = explode("\n", $this->responseHeader, 2);
		if(preg_match('|^HTTP/1.[01] ([0-9]+) (.*)$|', $firstRow, $match)) {
			$text = $match[2];
			return (int) $match[1];
		}
		
		else {
			$text = null;
			return false;
		}
	}


	public function setHeader($name, $value, $replace = false) {
		$index = false;

		// Budeme nahrazovat
		if($replace) {
			foreach($this->requestHeaders as $k => $v) if(startsWith(strtolower($v), strtolower($name))) {
				$index = $k;
				break;
			}
		}

		// Ulozime
		if(!$index) $this->requestHeaders[] = "$name: $value";
		else $this->requestHeaders[$index] = "$name: $value";
	}

	/**
	* Hlavicky vsecky s danym nazvem
	*/
	function getHeaders($headerName) {
		$headerName = strtolower($headerName);

		$ret = array();

		foreach($this->responseHeaders as $row) {
			@list($name, $value) = explode(':', $row, 2);
			if($name && !is_null($value) && strtolower($name) == $headerName) $ret[] = trim($value);
		}

		return $ret;
	}

	/**
	* Najde hlavicku s danym nazvem
	*/
	function getHeader($headerName) {
		$ret = $this->getHeaders($headerName);
		if(!$ret) return false;
		else return $ret[0];
	}

	/**
	* Automaticky nastavi cookies podle prijatych hlavicek
	*/
	function autosetCookies() {
		// Projdeme vsecky Set-Cookie v hlavicce
		foreach($this->getHeaders('set-cookie') as $row) {
			$this->debug("Nastavuju novou cookie: $row");

			$rows = explode(';', $row);

			$cookie = array();

			// Nazev a hodnota
			@list($name, $value) = explode('=', array_shift($rows), 2);

			// Dalsi parametry
			foreach($rows as $row) {
				@list($n, $v) = explode('=', $row, 2);
				$cookie[strtolower(trim($n))] = $v;
			}

			// Ulozime
			$this->setCookie($name, $value, $cookie);
		}
	}

	/**
	* Seznam cookies pro danou cestu
	*/
	public function getCookieString($path = null) {
		$ret = array();

		$this->debug("Hledam cookies pro $path");

		// Projdeme vsecky ulozene cookies
		foreach($this->cookies as $cookie) {
			// Nesedi do dane cesty
			if($path && isset($cookie['path']) && !startsWith($path, $cookie['path'])) continue;

			$ret[] = $cookie['name'] . '=' . $cookie['value'];
		}

		// Vysedek
		return implode(';', $ret);
	}

	/**
	* Jednoduchy pozadavek
	*/
	public function request2($page, $params = null) {
		return $this->request($this->server . $page, $params);
	}

	/**
	* Nastavi cookie
	*/
	public function setCookie($name, $value, $params = null) {
		// Nachystame kolacek
		$cookie = array('name' => $name, 'value' => $value);
		if(is_array($params)) $cookie = array_merge($cookie, $params);

		// Zkousime, zda uz neexistuje
		$exists = false;
		foreach($this->cookies as $index => $c) if($c['name'] == $name) {
			// Rozdilna cesta
			if(isset($c['path']) && isset($cookie['path']) && $c['path'] != $cookie['path']) continue;

			$exists = true;
			break;
		}

		// Neexistuje
		if($exists) $this->cookies[$index] = $cookie;
		else $this->cookies[] = $cookie;
	}

	/**
	* Vypsat ladici hlasku
	*/
	private function debug($txt) {
		if(!self::$allowDebug) return;

		$a = debug_backtrace();
		array_pop($a);
		$item = array_pop($a);

		$cas = date('H:i:s');
		echo @("$cas: " . ($this->id ? "$this->id - " : '') . $this->myCounter . " {$item['file']} on {$item['line']} - $txt<br />\n");
	}

	/**
	* Obnoveni sezeni (nacteni cookies ze session)
	*/
	public function restoreSession() {
		if(!$this->id) return;

		if(isset($_SESSION['browser-cache'][$this->id])) $this->cookies = $_SESSION['browser-cache'][$this->id];
		else $this->cookies = array();
	}

	/**
	* Ulozeni sezeni
	*/
	public function saveSession() {
		if(!$this->id) return;

		$_SESSION['browser-cache'][$this->id] = $this->cookies;
	}
	
	public function login($name = null, $pass = null) {
		// Clear auth
		if(empty($name)) {
			foreach($this->requestHeaders as $k => $v) if(startsWith($v, 'Authorization:')) unset($this->requestHeaders[$k]);
		}
		
		// Set username and pass
		else {
			$auth = base64_encode("$name:$pass");
			$this->requestHeaders[] = "Authorization: Basic $auth";
		}
	}
}



/**
* Vyjimka pro chybe browseru
*/
class BrowserException extends Exception {
	protected $browserInfo;

	function __construct($msg, $br = null) {
		// Vse podle parenta
		parent::__construct($msg);


		// Informace o browseru
		if($br) {
			$this->browserInfo = array();

			foreach($br as $k => $v) $this->browserInfo[$k] = $v;
		}
	}

	function getBrowser() {
		return $this->browserInfo;
	}
}

class BrowserFile {
	private $path;
	
	function __construct($path) {
		if(!file_exists($path)) throw new \Exception("File not found");
		
		$this->path = $path;
	}
	
	function getContents() {
		return file_get_contents($this->path);
	}
}

