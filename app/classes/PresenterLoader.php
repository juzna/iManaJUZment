<?php

class PresenterLoader extends Nette\Application\PresenterLoader {

  public function __construct($baseDir) {
    parent::__construct($baseDir);
  }
  
  /**
   * Formats presenter class name from its name.
   * @param  string
   * @return string
   */
  public function formatPresenterClass($presenter)
  {
    $x = explode(':', $presenter);
    $last = array_pop($x);
    
    $ret = 'Mods\\';
    foreach($x as $v) $ret .= ucfirst($v) . "\\";
    
    $ret .= "{$last}Presenter";
    
    return $ret;
  }



  /**
   * Formats presenter name from class name.
   * @param  string
   * @return string
   */
  public function unformatPresenterClass($class)
  {
    $x = explode('\\', $class);
    array_shift($x); // Remove 'Mods\\' prefix
    $last = array_pop($x);
    
    $ret = '';
    foreach($x as $v) $ret .= strtolower($v) . ':';
    
    $ret .= substr($last, 0, -9);
    
    return $ret;
  }
}
