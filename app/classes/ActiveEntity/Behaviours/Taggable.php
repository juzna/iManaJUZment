<?php

namespace ActiveEntity\Behaviours;


class Taggable extends BaseBehaviour {
  const CLASSNAME = 'ActiveEntity\\Behaviours\\Taggable';
  
  public static function setDefinition($className, $args) {
    if(!isset($args['targetEntity'])) throw new \Exception("Taggable behaviour expects definition of targetEntity");
    
    // Callback creator
    $cb = function($name) {
      return array(Taggable::CLASSNAME, $name);
    };
    
    self::hasMethod('hasTag', $cb('callHasTag'));
    self::hasMethod('addTag', $cb('callAddTag'));
    self::hasMethod('getTagList', $cb('callGetTagList'));
  }
  
  
  public static function callHasTag($className, $oThis, $tagName) {
    foreach($oThis->Tags as $tag) {
      if($tag->name == $tagName) return true;
    }
    return false;
  }
  
  public static function callAddTag($className, $oThis, $tagName) {
    if(!self::callHasTag($className, $oThis, $tagName)) {
      $prop = $className::_getBehavioralConfigVar(self::CLASSNAME, 'targetEntityProperty');
      $teClassName = $className::_getBehavioralConfigVar(self::CLASSNAME, 'targetEntity');
      
      // Create new tag     
      $teObj = new $teClassName;
      $teObj->$prop = $oThis;
      $teObj->name = $tagName;
      
      $oThis->Tags[] = $teObj;
    }
  }
  
  public static function callGetTagList($className, $oThis, $tagName) {
    $ret = array();
    foreach($oThis->Tags as $item) $ret[] = $item->name;
    return $ret;
  }
}

