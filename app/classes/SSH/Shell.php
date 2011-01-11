<?php


class Shell extends \Nette\Object {
  /**
   * @var ISocket Socket for communication
   */
  protected $socket;

  /**
   * @var resource Standard error stream of shell connection
   */
  protected $stderr;

  /**
   * @var string Input string buffer
   */
  protected $buffer;

  /**
  * Shell prompt parameters
  */
  protected $shellPrompt = '/^([a-z0-9-_@]+)(?:[(]([a-z0-9-_+]+)[)]|:~)?([#>$]) /iUm';
  protected $eol = "\r\n";
  protected $timeout = 5;


  function __construct(ISocket $socket, $stderr = null) {
    $this->socket = $socket;
    $this->stderr = $stderr;

    $socket->setReader(array($this, 'dataAvailable')); // Set data callback
  }

  /**
   * Callback: New data available on socket, process it
   * @param string $data
   * @return void
   */
  public function dataAvailable($data) {
    $this->buffer .= $data;
  }

  /**
   * Execute command on shell
   * @param string $cmd
   * @param bool $writeNewLine
   * @return void
   */
  public function exec($cmd, $writeNewLine = true) {
    if($writeNewLine) $cmd .= $this->eol;
    $this->socket->write($cmd);
  }

  /**
   * Waits for prompt and return data before it
   * @throws Exception
   * @param int $timeout
   * @return string
   */
  public function waitForPrompt($timeout = null) {
    if(!isset($timeout)) $timeout = $this->timeout;
    $timeEnd = microtime(true) + $timeout; // Time, when we should finish

    while(true) {
      $timeRest = $timeEnd - microtime(true);
      if($timeRest <= 0) throw new \Exception("Time out");
      if(!$this->socket->waitForData($timeRest)) throw new \Exception("No data received in time limit");

      // Read data
      $this->socket->read();

      if(preg_match($this->shellPrompt, $this->buffer, $match)) {
        $pos = strpos($this->buffer, $match[0]);

        // Get return data
        $ret = substr($this->buffer, 0, $pos);

        // Remove it from buffer
        $this->buffer = substr($this->buffer, $pos + strlen($match[0]));

        return $this->sanitize($ret);
      }
    }
  }

  /**
   * Execute command on shell and wait for output
   * @param string $cmd
   * @param bool $writeNewLine
   * @return string
   */
  public function execWait($cmd, $writeNewLine = true) {
    $this->exec($cmd, $writeNewLine);

    $ret = $this->waitForPrompt();

    // First row is repeating the command, skip it
    $pos = strpos($ret, "\n");
    if($pos !== false) $ret = substr($ret, $pos + 1);

    return trim($ret);
  }

  /**
   * Remove special characters
   * @param string $data
   * @return string
   */
  public function sanitize($data) {
    return preg_replace('/\x1b\[\d*(;\d+)?m/', '', $data);
  }
}
