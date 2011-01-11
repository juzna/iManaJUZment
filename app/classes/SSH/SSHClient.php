<?php

/**
 * Simple SSH client
 */
class SSHClient extends \Nette\Object {
	/**
	 * host to which we want to connect
	 * @var string
	 */
	private $host;

  /**
	 * port to which we are connected
	 * @var integer
	 */
	private $port;

	/**
	* If we are already logged in
	* @var bool
	*/
	private $loggedIn = false;

	/**
	* PHP's resource (returned by ssh2_connect)
	*/
	private $connection = null;

	/**
	* Shell
	*/
	private $shell = null;
  protected $terminalType = 'vt100';

	/**
	* Hold list of streams (because of error in libssh2 with unset)
	*/
	private static $streamList = array();

  /**
   * @var string Welcome message from server
   */
  public $welcomeMessage;

  /**
   * @var bool Show debugging information to stdout
   */
  protected $debug = false;


  public function __construct($host, $port = 22) {
    $this->host = $host;
    $this->port = $port;
  }

	/**
	* Connect
	*/
	public function connect() {
		// Login methods
		$methods = array(
			'kex' => 'diffie-hellman-group1-sha1',
			#  'client_to_server' => array(
			#    'crypt' => '3des-cbc',
			#    'comp' => 'zlib,none'),
			#  'server_to_client' => array(
			#    'crypt' => 'aes256-cbc,aes192-cbc,aes128-cbc',
			#    'comp' => 'zlib,none')
		);

		// Callbacks for libssh2
		$callbacks = array(
			'disconnect' => array(&$this, '_ssh_disconnect'),
			//'macerror' => array(&$this, '_ssh_macerror'),
		);

		// Connect!
		$this->connection = ssh2_connect($this->host, $this->port, $methods, $callbacks);
		if(!$this->connection) throw new \Exception("Unable to connect SSH '$this->host'");

    // Let sub-casses know that we're connected
    $this->connected();

		return true;
	}

  function connected() {}

	/**
	* Callback when ssh gets disconnected
	*/
	private function _ssh_disconnect($reason, $message, $language) {
		printf("Server disconnected with reason code [%d] and message: %s\n", $reason, $message);

		$this->loggedIn = false;
	}

	/**
	* Fingerprint of server
	* @return string 32 chars, MD5 hex
	*/
	public function getFingerprint() {
		return ssh2_fingerprint($this->connection, SSH2_FINGERPRINT_MD5 | SSH2_FINGERPRINT_HEX);
	}

	/**
	* Login with username and password
	* @return bool If passed
	*/
	public function authPass($login, $pass) {
		$this->loggedIn = $ret = ssh2_auth_password($this->connection, $login, $pass);
		if(!$ret) throw new Exception("Unable to login");
		
		return $ret;
	}

	/**
   * Execute a command via ssh2_exec
	 * @return stream
	 */
	public function exec($cmd) {
		$stream = ssh2_exec($this->connection, $cmd);

		// Nastavime na blokujici mod, jinak se seka
		stream_set_blocking($stream, true);

		return $stream;
	}

	/**
	* Spustime prikaz a nacteme vysledek jako string
	*/
	public function execWait($cmd) {
		ErrorHandler::clear();
		if(!$stream = @ssh2_exec($this->connection, $cmd, false, array(), 240, 160, SSH2_TERM_UNIT_CHARS)) {
			@list($errno, $errstr) = ErrorHandler::getLast();

			throw new SSHException($errstr);
		}
		self::$streamList = $stream;

		// Nastavime na blokujici mod, jinak se seka
		stream_set_blocking($stream, true);

		// Cteme data
		$ret = '';
		while(!feof($stream)) $ret .= fread($stream, 10240);


		return rtrim($ret);
	}




  /*******   Shell functions  *****/

	/**
	 * Get shell connection
   * @return Shell
	 */
	public function getShell() {
    if(!isset($this->shell)) $this->createShell();
    return $this->shell;
  }

  /**
   * Create new shell connection
   */
  public function createShell($waitForInitialShell = true) {
    $shell = ssh2_shell($this->connection, $this->terminalType, array(), 480, 160, SSH2_TERM_UNIT_CHARS);
    $stderr = ssh2_fetch_stream($shell, SSH2_STREAM_STDERR);

    // Set as non-blocking
    stream_set_blocking($shell, false);
    stream_set_blocking($stderr, false);

    // Wrap it in a class
    $shellSocket = new SSHShellSocket($this->connection, $shell);

    // Initialize shell
    $this->shell = new Shell($shellSocket, $stderr);

    if($waitForInitialShell) $this->waitForInitialPrompt();
  }

  public function execShell($cmd, $writeNewLine = true) {
    return $this->getShell()->exec($cmd, $writeNewLine);
  }

  public function execShellWait($cmd, $writeNewLine = true) {
    return $this->getShell()->execWait($cmd, $writeNewLine);
  }

  /**
   * Waits for initial prompt after connecting to SSH server
   * @return void
   */
  public function waitForInitialPrompt() {
    static $gotIt = false;

    if(!$gotIt) {
      $this->welcomeMessage = $this->getShell()->waitForPrompt();
      $gotIt = true;
    }
  }

  public function setDebug($debug) {
    $this->debug = $debug;
  }

  public function getDebug() {
    return $this->debug;
  }
}
