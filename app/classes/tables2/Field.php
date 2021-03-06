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


namespace Juz\Tables;

/**
* Represents field of table
*/
class Field {
  /** @var string */
  public $name;

  /** @var string */
  public $title;

  /** @var string */
  public $variable;

  /** @var string */
  public $content;

  /** @var \ActiveEntity\LinkMetadata Link parameters */
  public $link;

  /** @var string */
  public $icon;

  /** @var bool Whether to show this field by default */
  public $show;

  /** @var string|array Helpers to use for drawing this field */
  public $helper;

  /** @var array Misc parameters */
  public $parameters = array();


  /**
   * @param string $name Name of field
   * @param array $params Array of additional parameters
   */
  public function __construct($name, $params = null) {
    $this->name = $name;
    
    if(is_array($params)) foreach($params as $k => $v) {
      if(property_exists($this, $k)) $this->$k = $v;
      else $this->parameters[$k] = $v;
    }
  }
  
  public function __get($name) {
    return property_exists($this, $name) ? $this->$name : (isset($this->parameters[$name]) ? $this->parameters[$name] : null);
  }
  
  public function __set($name, $value) {
    if(property_exists($this, $name)) $this->$name = $value;
    else $this->parameters[$name] = $value;
  }
}
