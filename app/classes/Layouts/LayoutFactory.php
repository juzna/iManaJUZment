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


use Nette\Web\HttpRequest;

/**
 * Factory class for creating layouts
 */
class LayoutFactory implements ILayoutFactory {
  protected static $singletons = array();

  function __construct() {
    throw new \InvalidStateException('Cannot instantiate static class');
  }

  /**
   * Get layout for this HTTP request
   * @param HttpRequest $request
   * @return ILayout
   */
  public static function getLayout(HttpRequest $request, $activate = true) {
    $className = self::getClassName($request);
    if(!$className) $className = 'BasicLayout';
    if(!class_exists($className)) throw new \Nette\Application\BadRequestException("Layout class for this http request not exists: $className");

    // Instantiate layout
    if(!isset(self::$singletons[$className])) {
      $layout = new $className($request);
      if($activate) $layout->activate();
      self::$singletons[$className] = $layout;
    }

    return self::$singletons[$className];
  }

  /**
   * Get active layout
   * @return ILayout
   */
  public static function getActiveLayout() {
    return self::getLayout(\Nette\Environment::getHttpRequest(), false);
  }

  /**
   * Get class name of layout to be used based on HTTP request
   * @param HttpRequest $request
   * @return string
   */
  protected static function getClassName(HttpRequest $request) {
    if($request->getHeader('ajax-content', 0) || $request->getQuery('ajax-content', 0)) return 'AjaxContentLayout';

    // Layout name given in cookie
    if($layout = $request->getCookie('layout') or $layout = $request->getQuery('layout')) {
      return ucfirst($layout) . 'Layout';
    }

    // No layout known, use the base one
    return 'BasicLayout';
  }

  /**
   * Get list of supported layouts
   * @return array Map: name -> description
   */
  public static function getSupportedLayouts() {
    return array(
      'basic'       => "Basic",
      'basicJQuery' => "jQuery",
      'simple'      => "Simple",
      'ajaxContent' => "Content for AJAX requests",
      'tablet'      => "iPad",
    //  'phone'       => 'Phone'
    );
  }
}
