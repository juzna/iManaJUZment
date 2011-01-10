<?php
require_once __DIR__ . '/bootstrap.php';

/*

$c = ssh2_connect('85.132.153.24');
ssh2_auth_password($c, 'pesokes', 'morpheus');
$s = ssh2_shell($c, 'vt100');
stream_set_blocking($s, false);

var_dump('Shell:', $s, fgets($s));



while(true) {
  echo '----------------';

  $fd = ssh2_get_stream($c);
  var_dump($fd);
  $r = array($fd); $w = $e = array();

  if(!$x = stream_select($r, $w, $e, 10)) break;
  var_dump('Select', $x);
  var_dump(fread($s, 1024));
}

exit;
$x = stream_select($r, $w, $e, 10);
var_dump('Select', $x);


echo "Sleeping";
sleep(1);




exit;
echo '----------------';

*/

$client = new SSHClient('85.132.153.24');
$client->connect();
$client->authPass('pesokes', 'morpheus');



var_dump($client->execShellWait("ls -la"));

