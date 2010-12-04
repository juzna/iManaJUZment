<?php

namespace ActiveEntity\Behaviours;

class Geographical extends BaseBehaviour {
  public static function setDefinition($className, $args) {
    self::hasColumn('latitude', 'decimal');
    self::hasColumn('longitude', 'decimal');
    
    // TODO: self::hasMethod('getPosition', 'callGetPosition');
  }
  
  static function callGetPosition($cls, $oThis) {
    return array($oThis->latitude, $oThis->longitude);
  }  
}

