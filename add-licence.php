<?php
$path = @$_SERVER['argv'][1];
if(!file_exists($path)) die("File not found");

$rows = file($path);
array_shift($rows);

$data = file_get_contents('./licence-small') . implode("", $rows);

file_put_contents($path, $data);

