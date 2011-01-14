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


namespace Tables;

/**
* Represents field of table
*/
class TableField {
  public $name;
  public $title;
  public $variable;
  public $contentCode;
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

  public function renderContent() {
    if(!empty($this->parameters['link'])) $this->_renderLink();
    else $this->_render();
  }

  protected function _renderLink($fixVariableNames = true) {
    /** @var $link ActiveEntity\Annotations\Link */
    $link = $this->parameters['link'];

    // Prepare target
    $target = $link->module ? ":{$link->module}:" : '';
    $target .= $link->presenter . ':';
    $target .= $link->action ? "$link->action!" : $link->view;

    // Prepare parameters
    $params = array();
    foreach($link->params as $k => $p) {
      $prefix = is_int($k) ? '' : "$k => ";

      if(substr($p, 0, 1) === '$') {
        if($fixVariableNames) $params[] = $prefix . '$item->' . substr($p, 1);
        else $params[] = $prefix . $p;
      }
      else $params[] = $prefix . var_export($p, true);
    }
    $params = implode(', ', $params);

    $href = '{plink ' . $target . ($params ? ", $params" : '') . '}';
    echo '    <a href="' . $href . '">';
    $this->_render();
    echo '</a>';
  }

  protected function _render() {
    if($this->variable) echo '{$item->' . $this->variable . (!empty($this->parameters['show']->helper) ? '|' . $this->parameters['show']->helper : '') . '}';
    else echo preg_replace('/(\\{[!]?\\$)([a-z0-9_]+)/i', '\\1item->\\2', $this->contentCode);
  }
}
