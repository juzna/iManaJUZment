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
    if($this->variable) echo '{$item->' . $this->variable . '}';
    else echo $this->contentCode;
  }
}
