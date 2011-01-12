<?php

class Arrays {
  /**
   * Make array indexed
   * @param array $arr
   * @param string $field
   * @return array Indexed array
   */
  static function indexBy($arr, $field) {
    $ret = array();
    if(is_array($arr)) foreach($arr as $row) $ret[is_array($row) ? $row[$field] : $row->$field] = $row;
    return $ret;
  }

  /**
   * Create multi-indexed array
   * @param array $arr
   * @param string $fields
   * @return array
   */
  static function indexMultiple($arr, $fields) {
    // Convert field list to array
    if(is_string($fields)) $fields = preg_split('/[, ]/', $fields);
    elseif(is_array($fields)) { /* dummy */ }
    else throw new \InvalidArgumentException("Expected to be array or string");

    // Create assigning function (which will look like $ret[$item['a']][$item['b']][] = $item;
    $code = '$ret';
    $rest = count($fields);
    foreach($fields as $field) {
      $rest--;
      if($field !== '') $code .= "[is_array(\$item) ? \$item['$field'] : \$item->$field]";
      else {
        if($rest > 1) throw new \InvalidArgumentException("Autoindex field must be the last one");
        $code .= '[]';
      }
    }
    $code .= ' = $item;';
    $func = create_function('&$ret, $item', $code);

    $ret = array();
    foreach($arr as $item) $func($ret, $item);

    return $ret;
  }

  /**
   * array_map which is traversing to given depth
   * @param int $level
   * @param callback $cb
   * @param array $arr
   * @return array
   */
  static function mapDeep($level, $cb, $arr) {
    if($level == 1) return array_map($cb, $arr);
    elseif($level > 1) {
      --$level;
      return array_map(function($item) use($cb, $level) {
        return Arrays::mapDeep($level, $cb, $item);
      }, $arr);
    }
    else return false;
  }

  /**
   * Switch rows and cols in two-dimensional array
   */
  function tr2td($arr) {
    $ret = array();
    foreach($arr as $tr=>$td) {
      foreach($td as $k=>$v) {
        $ret[$k][$tr] = $v;
      }
    }
    return $ret;
  }

  /**
   * Check if array keys are only numbers
   */
  static function numericKeys(&$arr) {
    foreach(array_keys($arr) as $f) if(!is_numeric($f)) return false;
    return true;
  }

  /**
   * Check if array has only numeric values
   */
  static function numericArray(&$arr) {
    foreach($arr as $f) if(!is_numeric($f)) return false;
    return true;
  }

  /**
   * Puts an element onto a specific position into an array.
   * Keeps original size.
   */
  static function insertElement(&$array, $value, $pos) {

    // get current size ...
    $size = count($array);

    // if position is in array range ...
    if ($pos < 0 && $pos > $size) {
      return false;
    }

    // shift values below ...
    for($i = $size-1; $i >= $pos; $i--) {
      $array[$i+1] = $array[$i];
    }

    // now put in the new element.
    $array[$pos] = $value;
    return true;
  }

  /**
   * Moves an element from one position to the other.
   * All items between are shifted down.
   */
  static function moveElement(&$array, $from, $to) {

    // get current size ...
    $size = count($array);

    // destination and source have to be among the array borders!
    if ($from < 0 && $from > $size && $to < 0 && $to > $size) {
      return false;
    }

    // backup the element we have to move ...
    $moving_element = $array[$from];

    if($from > $to) {
      // shift values between downwards ...
      for ($i = $from-1; $i >= $to; $i--) {
        $array[$i+1] = $array[$i];
      }
    } else {
    for($i = $from; $i < $to; $i++) {
      $array[$i] = $array[$i + 1];
    }
    }

    // now put in the element which was to move ...
    $array[$to] = $moving_element;
    return true;
  }

  /**
   * Switch two elements in array
   */
  static function swapElements(&$array, $first, $second) {
    // One of 'em not exists
    if(!isset($array[$first]) || !isset($array[$second])) return false;

    $cache = $array[$first];

    $array[$first] = $array[$second];
    $array[$second] = $cache;

    return true;
  }

  /**
   * @static
   * @param array $arr
   * @param string $field Field name
   * @return array
   */
  static function pluck($arr, $field) {
    $ret = array();
    while(list($key, $row) = each($arr)) {
      $ret[$key] = @$row[$field];
    }
    return $ret;
  }

  /**
   * Get object properties
   * @param object $obj
   * @return array
   */
  static function fromObject($obj) {
    return is_object($obj) ? get_object_vars($obj) : $obj;
  }

  /**
   * Fetch pairs
   * @param array $srcArray
   * @param string $keyName
   * @param string $valueName
   * @return array
   */
  static function collect($srcArray, $keyName, $valueName) {
    if(!is_array($srcArray)) return;

    $ret = array();

    foreach($srcArray as $item) {
      if($keyName) {
        if(isset($item[$keyName])) $ret[$item[$keyName]] = @$item[$valueName];
      } else $ret[] = @$item[$valueName];
    }

    return $ret;
  }
}
