<?php

use Nette\Templates\ITemplate,
  Nette\Templates\Template,
  Nette\Templates\FileTemplate;
  

/**
 * Template which consists of explicitly defined blocks
 * Should be extended by layout template to render these blocks
 */
class CodeTemplate extends \Nette\Object implements ITemplate {
  protected $blocks;
  protected $variables;
  protected $tpl;
  
  protected static $specialVariables = array('layout', '_extends');
  
  /**
   * @param ITemplate $layout Layout templates
   */
  public function __construct($layout = null) {
    $this->tpl = $layout;
  }
  
  /************* Blocks manipulation **************/

  public function setBlock($block, $content) {
    $this->blocks[$block] = $content;
  }
  
  public function getBlock($name) {
    return isset($this->blocks[$name]) ? $this->blocks[$name] : null;
  }


  
  
  /************ Magic methods *************/
  
  public static function isSpecialVariable($var) {
    return in_array($var, self::$specialVariables);
  }
  
  public function &__get($name) {
    if(self::isSpecialVariable($name)) return $this->variables[$name]; 
    else return $this->getBlock($name);
  }
  
  public function __set($name, $value) {
    if(self::isSpecialVariable($name)) $this->variables[$name] = $value;
    else $this->setBlock($name, $value);
  }
  
  
  /************ ITemplate ****************/
    
  public function render() {
    // Create layout template
    if($layout = $this->layout) {
      if($layout instanceof ITemplate) $tpl = $layout;
      elseif(is_string($layout)) {
        $tpl = $this->tpl ?: new FileTemplate;
        $tpl->setFile($layout);
      }
      else throw new \Exception("Invalid layout file");
    }
    else throw new \Exception("Undefined state, missing layout template");
    
    
    // Set-up blocks
    $l = (object) null;
    foreach($this->blocks as $name => $content) {
      $l->blocks[$name][] = function() use ($content) { echo $content; };
    }
    $tpl->_l = $l;
    
    
    // Render it
    $tpl->render();
  }
}

