<?php

/**
* Prace se souradnicemi
* @package ServisWeb
* @subpackage Maps
*/

class Point {
	public $x;
	public $y;

	/**
	* Zda je prevracena osa Y
	* Normalni je ze roste smerem nahoru (jako v matematice, na mape)
	* Obracena je napriklad na obrazku (pocita se od leveho horniho rohu)
	*/
	public $reverseY;

	function __construct($x, $y, $reverseY = false) {
		$this->x = $x;
		$this->y = $y;
		$this->reverseY = $reverseY;
	}

	// Vzdalenost od naseho bodu k dalsimu
	function vzdalenost($x, $y) {
		return sqrt(pow($x - $this->x, 2) + pow($y - $this->y, 2));
	}

	/**
	* Uhel od naseho bodu k cilovemu (ve stupnich)
	* @param $x int Souradnice X
	* @param $y int Souradnice Y
	* @param $fromThisPoint bool Pokud je true, spocita se uhel od zadanych souradnic k tomuto bodu
	*/
	function uhel($x, $y, $fromThisPoint = false) {
		// Zakladni uhel
		$uhel = @rad2deg(atan(abs($diffY = $y - $this->y) / abs($diffX = $x - $this->x)));

		// Obracena osa Y
		if($this->reverseY) $diffY = - $diffY;

		if($fromThisPoint) {
			$diffX = - $diffX;
			$diffY = - $diffY;
		}

		// Dopocet
		if($diffX >= 0 && $diffY >= 0);
		elseif($diffX < 0 && $diffY >= 0) $uhel = 180 - $uhel;
		elseif($diffX < 0 && $diffY < 0) $uhel = 180 + $uhel;
		else $uhel = 360 - $uhel;

		return $uhel;
	}
}

