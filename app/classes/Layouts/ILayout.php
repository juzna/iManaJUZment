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
 * Layout for entire page
 */
interface ILayout {
  /**
   * Get name of this layout
   * @return string
   */
  function getName();

  /**
   * Set-up environment or global variables which are needed for this layout (like AjaxConent can disable Debug panel)
   * @return void
   */
  function activate();

  /**
   * Get outer template for given layout
   * @param PresenterRequest $presenterRequest
   * @return string Path to template
   */
  function getLayoutTemplateFile(PresenterRequest $presenterRequest = null);

  /**
   * Get list of required Javascript files
   * @param PresenterRequest $presenterRequest
   * @return array of string
   */
  function getRequiredJsFiles(PresenterRequest $presenterRequest = null);

  /**
   * Get list of required Css files
   * @param PresenterRequest $presenterRequest
   * @return array of string
   */
  function getRequiredCssFiles(PresenterRequest $presenterRequest = null);

  /**
   * Renders special form control
   * @param IFormControl $control
   * @return void
   */
  function renderFormControl(IFormControl $control);

  /**
   * Renders a component
   * @param  $name
   * @param  $definition
   * @return void
   */
  function renderComponent($name, $definition);
}
