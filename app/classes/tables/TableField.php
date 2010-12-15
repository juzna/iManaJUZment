<?php

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

  protected function _renderLink() {
    /** @var $link ActiveEntity\Annotations\Link */
    $link = $this->parameters['link'];
    if($link->presenter) {
      if(strpos($link->presenter, ':') === -1) $target = "$link->presenter:$link->view";
      else $target = ":$link->presenter:$link->view";
    }
    else $target = $link->view;

    $params = array();
    foreach($link->params as $p) {
      if(substr($p, 0, 1) === '$') $params[] = '$item->' . substr($p, 1);
      else $params[] = var_export($p, true);
    }
    $params = implode(', ', $params);

    echo '<a href="{plink ' . $target . ($params ? ", $params" : '') . '}">';
    $this->_render();
    echo '</a>';
  }

  protected function _render() {
    if($this->variable) echo '{$item->' . $this->variable . '}';
    else echo preg_replace('/(\\{[!]?\\$)([a-z0-9_]+)/i', '\\1item->\\2', $this->contentCode);
  }
}
