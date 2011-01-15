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
 
class AjaxContentLayout extends DefaultLayout {
  /**
   * Set-up environment or global variables which are needed for this layout
   * @return void
   */
  function activate() {
    \Nette\Debug::$showBar = false;
  }

  /**
   * Get outer template for given layout
   * @param PresenterRequest $presenterRequest
   * @return string Path to template
   */
  function getLayoutTemplateFile(Nette\Application\PresenterRequest $presenterRequest = null) {
    return false;
  }

}

