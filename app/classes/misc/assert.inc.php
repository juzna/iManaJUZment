<?

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