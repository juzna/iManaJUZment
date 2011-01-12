<?php


abstract class Date {
  /**
  * Parse date in misc format and return it in YYYY-MM-DD
  */
  static function parse($dat) {
    if(empty($dat)) return null;

    if(preg_match('/^([12][0-9]{3})([0-9]{2})([0-9]{2})$/', "$dat", $m)) $dat = date('Y-m-d', mktime(0, 0, 0, $m[2], $m[3], $m[1]));
    elseif(preg_match('/^([12][0-9]{3})-([0-9]{1,2})-([0-9]{1,2})(?: ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}))?$/', "$dat", $m)); //mktime(0, 0, 0, $m[2], $m[3], $m[1]);
    elseif(preg_match('/^([0-9]{1,2}).\s*([0-9]{1,2}).\s*([12][0-9]{3})(?: ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}))?$/', "$dat", $m)) $dat = date(@$m[4] ? 'Y-m-d H:i:s' : 'Y-m-d', mktime(@$m[4], @$m[5], @$m[6], $m[2], $m[1], $m[3]));

    // dd.mm.
    elseif(preg_match('/^([0-9]{1,2})\\.\s*([0-9]{1,2})\\.$/', "$dat", $m)) $dat = date('Y-m-d', mktime(0, 0, 0, $m[2], $m[1], date('Y')));
    elseif(preg_match('/^([0-9]{1,2})\\.\s*([0-9]{1,2})\\.([0-9]{1,2})$/', "$dat", $m)) $dat = date('Y-m-d', mktime(0, 0, 0, $m[2], $m[1], $m[3] < 70 ? "20{$m[3]}" : "19{$m[3]}"));

    if($tim = @strtotime($dat)) {
      $date = date('Y-m-d', $tim);
      if((int) date('His', $tim)) $date .= ' ' . ($time = date('H:i:s', $tim));
      return $date;
    }

    return $dat;
  }

  /**
  * Get localized date
  */
  static function localized($dat) {
    if(!is_numeric($dat)) $dat = strtotime($dat);
    return date('j.n.Y', $dat);
  }
  
  /**
  * Get difference of two dates
  * @param $start timestamp Zacatek
  * @param $konec timestamp Konec
  * @param $interval (day|month|year) Urcuje v jakych intervalech pocitame
  */
  static function time_diff($start, $konec, $interval = 'month', $floor = false) {
      if(!is_numeric($start)) $start = strtotime($start);
    if(!is_numeric($konec)) $konec = strtotime($konec);


    $sign = 1;
    // Jestli je to naopak, tak prohodime start a konec
    if($start > $konec) {
      $sign = -1;
      list($start, $konec) = array($konec, $start);
    }

    switch($interval) {
      case 'day':
      case 'den':
      case 'd':
        $ret = ($konec - $start) / 3600 / 24;
        break;

      case 'mesic':
      case 'month':
      case 'm':
        $ret = 0;
        while($start < $konec) {
          $t = strtotime('+1 month', $start);
          if($t < $konec) {
            $ret++;
            $start = $t;
          } else {
            $ret += ($konec - $start) / 3600 / 24 / 31;
            break;
          }
        }
        break;

      case 'year':
      case 'rok':
      case 'y':
      case 'r':
        $ret = 0;
        while($start < $konec) {
          $t = strtotime('+1 year', $start);
          if($t < $konec) {
            $ret++;
            $start = $t;
          } else {
            $ret += time_diff($start, $konec, 'm') / 12;
            break;
          }
        }
        break;

    }

    if($floor) $ret = floor($ret);

    return $ret * $sign;
  }
}
