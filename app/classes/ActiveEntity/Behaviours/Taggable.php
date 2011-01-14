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

