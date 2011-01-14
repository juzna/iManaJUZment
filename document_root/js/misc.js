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
 * Like parseFloat, but always returns a number
 */
function parseFloat2(val) {
	val = parseFloat(val);
	return isNaN(val) ? 0.0 : val;
}

/**
 * Like parseInt, but always returns a number
 */
function parseInt2(val) {
  val = parseInt(val);
	return isNaN(val) ? 0.0 : val;
}

/**
 * Rounding with given precision
 * @param float number
 * @param int precision
 * @return float
*/
function round2(number, precision) {
	if(!precision) return Math.round(number);

	var mul = Math.pow(10, precision);
	return Math.round(number * mul) / mul;
}

/**
 * Ceil with precision
 * @param number
 * @param precision
 */
function ceil2(number, precision) {
	if(!precision) precision = 2;

	var mul = Math.pow(10, precision);
	return Math.ceil(number * mul) / mul;
}

/**
 * Floor with precision
 * @param number
 * @param precision
 */
function floor2(number, precision) {
	if(!precision) precision = 2;

	var mul = Math.pow(10, precision);
	return Math.floor(number * mul) / mul;
}
