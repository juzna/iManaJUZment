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

use Nette\Application\PresenterRequest;
use Nette\Forms\IFormControl;


/**
 * Default page layout
 */
abstract class DefaultLayout implements ILayout {
  // Needed CSS files
  protected static $cssFiles = array(
    'content.css', 'isp.css',
  );
  
  // All JS files
  protected static $jsFiles = array(
    'misc.js',
  );

  // List of features of this layout
  protected static $features = array();

  // Template to be used. Either this variable or getLayoutTemplateFile must be overriden
  protected $templateName = null;

  /**
   * Get name of this layout
   * @return string
   */
  function getName() {
    return get_class($this);
  }

  /**
   * Check if this layout has a given feature
   * @param string $name Feature name
   * @return bool
   */
  function hasFeature($name) {
    return in_array($name, static::$features);
  }

  /**
   * Set-up environment or global variables which are needed for this layout (like AjaxConent can disable Debug panel)
   * @return void
   */
  function activate() {
    // Nothing to do...
  }

  /**
   * Get outer template for given layout
   * @param PresenterRequest $presenterRequest
   * @return string Path to template
   */
  function getLayoutTemplateFile(PresenterRequest $presenterRequest = null) {
    if(isset($this->templateName)) return APP_DIR . '/templates/' . $this->templateName;
    else throw new \InvalidStateException('Template for this layout was not defined');
  }

  /**
   * Get list of required Css files
   * @param PresenterRequest $presenterRequest
   * @return array of string
   */
  function getRequiredCssFiles(PresenterRequest $presenterRequest = null) {
    return static::$cssFiles;
  }

  /**
   * Get list of required Javascript files
   * @param PresenterRequest $presenterRequest
   * @return array of string
   */
  function getRequiredJsFiles(PresenterRequest $presenterRequest = null) {
    return static::$jsFiles;
  }

  /**
   * Renders special form control
   * @param IFormControl $control
   * @return void
   */
  function renderFormControl(IFormControl $control) {
    throw new \NotImplementedException('Control not known');
    // TODO: Implement renderFormControl() method.
  }

  /**
   * Renders a component
   * @param  $name
   * @param  $definition
   * @return void
   */
  function renderComponent($name, $definition, $options = null) {
    $className = "Layout\\Components\\" . ucfirst($name);
    if(!class_exists($className)) throw new \InvalidArgumentException("Component '$name' not exists in this layout");

    return $className::render($definition, $this, $options);
  }
}
