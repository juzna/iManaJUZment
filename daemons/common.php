<?php

function runUnixSocketServer($name, $processor) {
  // Create server on socket
  $unixPath = TMP_DIR . '/sock/' . $name;
  @unlink($unixPath);
  if(!is_dir($socketDir = dirname($unixPath))) mkdir($socketDir, 0777);

  $socket = new TServerSocket("unix://$unixPath", -1);
  $socket->listen();
  while(true) {
	  $socket->select($processor);
	  echo "."; flush();
  }
}

