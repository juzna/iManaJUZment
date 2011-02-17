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


namespace Layout\Components;

use Nette\Templates\LatteMacros;

class TabPanel {
  /**
   * Renders tab-panel
   * @param array $definition Tab panel definition
   * @param \ILayout $layout Layout which is used
   * @return string
   */
  public static function render($definition, \ILayout $layout, $options = null) {
    if($layout->hasFeature('jquery')) return self::renderJQuery($definition, $options['macros']);
    else return self::renderSimple($definition, $options['macros']);
  }

  /**
   * Simple (static) renderer of tabpanel
   * @param  array $tabPanel Tabpanel definition
   * @return string
   */
  public static function renderSimple($tabPanel, LatteMacros $macros) {
    $ret = array();

    // Display header
    $ret[] = '<div class="tabPanel" id="tabPanel-' . $tabPanel['name'] . '">';
    $ret[] = '<ol class="tabPanelTabs">';
    $ret[] = '<?php $_tabPanelPage = $presenter->getParam("tabpanel_' . $tabPanel['name'] . '_page", "' . $tabPanel['pages'][0]['name'] . '"); ?>';
    foreach($tabPanel['pages'] as $page) {
      $codeHref = '<?php echo ' . $macros->macroLink('this, tabpanel_' . $tabPanel['name'] . '_page => ' . $page['name'], '') . ';?>';
      $codeIsActual = '<?php if($_tabPanelPage === ' .  var_export($page['name'], true) . ') echo " active"; ?>';

      $ret[] = '<li class="tab' . $codeIsActual .'">' .
        '<span class="left"></span><span class="right"></span>' .
        '<a class="content" href="' . $codeHref . '">'  .$page['title'] . '</a>' .
        '<div class="visDiv"></div>' .
        '</li>';
    }
    $ret[] = '</ol>';

    // Display individual tabs
    $ret[] = '<div class="tabPanelContent">';
    foreach($tabPanel['pages'] as $page) {
      $ret[] = '<?php if($_tabPanelPage === ' .  var_export($page['name'], true) . ') { ?>';
      $ret[] = "<div class=\"tabPage\" id=\"{$page['id']}\">";
      $ret[] = "<!--\n" . var_export($page, true) . "\n-->";
      $ret[] = '<?php ' . $macros->macroInclude('#' . $page['id'], '') . '; ?>';
      $ret[] = '</div>';
      $ret[] = '<?php } ?>';
    }

    // Implicit tab
    /* {
      $ret[] = '<?php if(!isset($tabpanel_page)) { ?>';
      $ret[] = "<div class=\"tabPage implicit\">Choose a tab</div>";
      $ret[] = '<?php } ?>';
    }*/
    $ret[] = '</div>';
    $ret[] = '</div>';

    return implode("\n", $ret);
  }

  /**
   * Renders tabpanel to be used with jQuery
   * @param array $tabPanel
   * @return string
   */
  public static function renderJQuery($tabPanel, LatteMacros $macros) {
    $ret = array();

    // Display header
    $ret[] = '<div id="tabPanel-' . $tabPanel['name'] . '">';
    $ret[] = '<ul>';
    foreach($tabPanel['pages'] as $page) {
      $codeHref = '<?php echo ' . $macros->macroLink('this, tabpanel_' . $tabPanel['name'] . '_page => ' . $page['name'] . ', ajax-content => 1', '') . ';?>';

      $ret[] = '<li><a href="' . $codeHref . '">'  .$page['title'] . '</a>';
    }
    $ret[] = '</ul>';
    $ret[] = '</div>';

    $ret[] = '<script type="text/javascript">$j(function() { $j("#tabPanel-' . $tabPanel['name'] . '").tabs(); });</script>';

    return implode("\n", $ret);
  }
}
