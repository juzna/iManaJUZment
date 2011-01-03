<?php

/**
* Common mathematic functions
*/
class Math {
  /**
  * Derivuje pole
  */
  function derive($arr, $first = false) {
	  $ret = array();

	  // Posledni polozka
	  $last = $first === false ? array_shift($arr) : $first;

	  $len = sizeof($arr);
	  while($len-- > 0) {
		  $x = array_shift($arr);
		  $ret[] = $x - $last;

		  $last = $x;
	  }

	  return $ret;
  }
}

