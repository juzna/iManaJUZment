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

class LayoutPanel implements \Nette\IDebugPanel {
  /**
   * Returns panel ID.
   * @return string
   */
  function getId() {
    return 'layout';
  }

  /**
   * Renders HTML code for custom panel.
   * @return void
   */
  function getPanel() {
    $list = \LayoutFactory::getSupportedLayouts();
    $selected = \Nette\Environment::getHttpRequest()->getCookie('layout');

    // Prepare template
    ob_start();
    require_once __DIR__ . '/LayoutPanel.phtml';
    return ob_get_clean();
  }

  /**
   * Renders HTML code for custom tab.
   * @return void
   */
  function getTab() {
    return LayoutFactory::getActiveLayout()->getName(); // . ', ' . count(LayoutFactory::getSupportedLayouts()) . ' available';
  }
}
