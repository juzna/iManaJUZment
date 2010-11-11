<?php

/**
 * My Application
 *
 * @copyright  Copyright (c) 2009 John Doe
 * @package    MyApplication
 */

namespace Mods;

use Nette\Application\Presenter,
  Nette\Environment;


/**
 * Base class for all application presenters.
 *
 * @author     John Doe
 * @package    MyApplication
 */
abstract class BasePresenter extends Presenter
{
  /**
   * Formats layout template file names.
   * @param  string
   * @param  string
   * @return array
   */
  public function formatLayoutTemplateFiles($presenter, $layout)
  {
    $appDir = Environment::getVariable('appDir') . '/../mods/';
    $path = '/' . str_replace(':', '/', $presenter);
    $pathP = substr_replace($path, '/templates', strrpos($path, '/'), 0);
    $list = array(
      "$appDir$pathP/@$layout.phtml",
      "$appDir$pathP.@$layout.phtml",
    );
    while (($path = substr($path, 0, strrpos($path, '/'))) !== FALSE) {
      $list[] = "$appDir$path/templates/@$layout.phtml";
    }
    return $list;
  }



  /**
   * Formats view template file names.
   * @param  string
   * @param  string
   * @return array
   */
  public function formatTemplateFiles($presenter, $view)
  {
    $appDir = Environment::getVariable('appDir') . '/../mods/';
    $path = '/' . str_replace(':', '/', $presenter);
    $pathP = substr_replace($path, '/templates', strrpos($path, '/'), 0);
    $path = substr_replace($path, '/templates', strrpos($path, '/'));
    return array(
      "$appDir$pathP/$view.phtml",
      "$appDir$pathP.$view.phtml",
      "$appDir$path/@global.$view.phtml",
    );
  }



}
