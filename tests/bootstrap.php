<?php
define('APP_DIR', __DIR__ . '/../app');
define('LIBS_DIR', __DIR__ . '/../libs');

require_once APP_DIR . '/bootstrap-common.php';

$what = @$_GET['what'] ?: 'AP';

function dump2($title, $data) {
  echo "<strong>$title</strong>\n";
  dump($data);
  echo "<hr />\n\n";
}

