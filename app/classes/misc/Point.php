<?php
/**
 * This file is part of the "iManaJUZment" - complex system for internet service providers
 *
 * Copyright (c) 2005 - 2011 Jan Dolecek (http://juzna.cz)
 *
 * iManaJUZment is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with iManaJUZment.  If not, see <http://www.gnu.org/licenses/gpl.txt>.
 *
 * @license http://www.gnu.org/licenses/gpl.txt
 */


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

