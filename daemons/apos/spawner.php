<?php
/**
* Handler, which spawns new APos childs when needed
*/

require_once(__DIR__ . '/../bootstrap.php');

class SpawnerHandler implements \Thrift\APos\SpawnerIf {
	// List of spawned processes
	private $processList = array();
	
	/**
	* Spawn new process for particular AP
	*/
	public function spawn($apid) {
		echo " Trying to start APos $apid\n";
		$unixPath = TMP_DIR . '/sock/apos-' . $apid;
		
		// Dir for logs
		if(!is_dir($dir = LOG_DIR . "/apos/")) mkdir($dir, 0777);
		
		$descriptorspec = array(
			0 => array('file', '/dev/null', 'r'),
			1 => array('file', LOG_DIR . "/apos/$apid.log", 'a'), // stdout is a pipe that the child will write to
			2 => array('file', LOG_DIR . "/apos/$apid.err", 'a'), // stderr is a file to write to
			3 => array("pipe", "w"), // marker, that child is ready
		);
		$childPath = __DIR__ . '/server.php';
		$process = proc_open("php -f '$childPath' $apid", $descriptorspec, $pipes);
		
		$this->processList[] = $process;
		
		// Wait for closing confirmation pipe
		stream_get_contents($pipes[3]);
		
		echo "  I'm back, working...\n";
		
		return true;
	}
}


// Create spawner handler
$handler = new SpawnerHandler();
$processor = new \Thrift\APos\SpawnerProcessor($handler);

// Run server loop
runUnixSocketServer('apos-spawner', $processor);

