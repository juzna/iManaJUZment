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


class AssertException extends Exception {}

class Assert {
	public static function equal($val, $expected, $msg = null) {
		if($val != $expected) throw new AssertException("Assertion failed: not equal '$val' != '$expected'; $msg");
	}
	
	public static function in($val, $list, $msg = null) {
		if(!in_array($val, $list)) throw new AssertException("Assertion failed: '$val' not in " . var_export($list, true) . "; $msg");
	}
	
	public static function true($cond, $msg = null) {
		if(!$cond) throw new AssertException("Assertion failed: condition evaluated to false; $msg");
	}
	
	public static function condition($cond, $msg = null) {
		self::true($cond, $msg);
	}
	
	public static function preg($val, $pattern, $msg = null) {
		if(!preg_match($pattern, $val)) throw new AssertException("Assertion failed: '$val' not matches '$pattern'; $msg");
	}
	
	public static function null($val, $msg = null) {
		if(isset($val)) throw new AssertException("Assertion failed: '$val' is not null; $msg");
	}
}