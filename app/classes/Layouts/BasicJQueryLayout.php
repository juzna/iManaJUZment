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
 
class BasicJQueryLayout extends DefaultLayout {
  // Needed CSS files
  protected static $cssFiles = array(
    'content.css', 'isp.css', 'input.css', 'window.css',
  //  'selectbox.css', 'tabpanel.css', 'contextmenu.css', 'wizard.css',
    'jquery.ui.selectmenu.css',
  );

  // All JS files
  protected static $jsFiles = array(
    'misc.js',
    'scriptaculous.js', 'scriptaculous-builder.js', 'scriptaculous-effects.js',
    'scope.js',
    'dialog.js',
  //  'tabpanel.js',
  //  'window.js',
    'input.js', 'input-date.js', 'input-number.js', 'input-net.js', 'input-color.js',
    'jquery.ui.selectmenu.js',
    'layout.js',
  );

  protected static $features = array('jquery');

  // Template file to be used
  protected $templateName = 'layout_basic.phtml';
}
