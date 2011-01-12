<?php
require_once __DIR__ . '/bootstrap.php';

$list = array_map(function($x) {
  return (object) array(
    'id' => $x,
    'square' => $x * $x,
    'cube'   => $x * $x * $x,
    'letter' => chr(ord('A') + $x),
  );
}, range(0, 10));

//print_r($list);

$indexed = Arrays::indexBy($list, 'letter');
//print_r($indexed);

$indexed2 = Arrays::indexMultiple($list, 'letter,id,');
print_r($indexed2);


