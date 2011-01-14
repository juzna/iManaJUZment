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


class LatteMacros extends \Nette\Templates\LatteMacros {

  protected $tabPanelStack = array();


  public function __construct() {
    parent::__construct();

    $this->macros['table'] = "<?php %:macroTable% ?>";
    $this->macros['tabpanel'] = "<?php %:macroTabPanel% ?>";
    $this->macros['/tabpanel'] = "<?php %:macroTabPanelEnd% ?>";
    $this->macros['tabpage'] = "<?php %:macroTabPage% ?>";
    $this->macros['/tabpage'] = "<?php %:macroTabPageEnd% ?>";
  }

  /**
   * Reads array and parse it -> return it directly
   * @param string $content
   * @return array
   */
  protected function parseArray($content) {
    $data = $this->formatArray($content, 'return ');
    return empty($data) ? array() : eval($data . ';');
  }

  public function macroTable($content, $modifiers) {
    $tableName = $this->fetchToken($content);
    $ds = $this->fetchToken($content);
    
    return '$presenter->drawTable(' . $this->formatString($tableName) . ', ' . $ds . $this->formatArray($content, ', ') . ')';
  }



  /********* Tab panel **********/

  // Start of tab-panel
  public function macroTabPanel($content, $modifiers) {
    $name = $this->fetchToken($content); // name [,] [params]

    // Add new tab-panel to stack
    array_unshift($this->tabPanelStack, array(
      'name' => $name,
      'pages' => array(),
    ));

    return '?><div class="tabPanel" id="tabPanel-' . $name . '"> <?php ';
  }

  // End of tab-panel
  public function macroTabPanelEnd($content, $modifiers) {
    // Remove this tab-panel from stack
    $tabPanel = array_shift($this->tabPanelStack);

    return '?>' . $this->renderTabPanelContents($tabPanel) . '</div><?php';
  }

  // Start of a page
  public function macroTabPage($content, $modifiers) {
    $tabPanel = &$this->tabPanelStack[0];

    $name = $this->fetchToken($content); // name, title, [params]
    $params = $this->parseArray($content);
    $title = isset($params[0]) ? $params[0] : ucfirst($name);

    $blockName = "tabpage_" . $this->tabPanelStack[0]['name'] . '_' . $name;

    $tabPanel['pages'][] = array(
      'id'  => $blockName,
      'name' => $name,
      'title' => $title,
      'options' => $params,
    );

    $tabPanel['.lastBlockName'] = $blockName;
    $this->namedBlocks[$blockName] = $blockName;
    return "{block $blockName}";
  }

  // End of a page
  public function macroTabPageEnd($content, $modifiers) {
    $blockName = $this->tabPanelStack[0]['.lastBlockName'];
    return "{/block $blockName}";
  }

  // Content renderer
  protected function renderTabPanelContents($tabPanel) {
    $ret = array();

    // Display header
    $ret[] = '<ol class="tabPanelTabs">';
    $first = true;
    foreach($tabPanel['pages'] as $page) {
      $codeHref = '<?php echo ' . $this->macroLink('this, tabpanel_page => ' . $page['name']) . ';?>';
      $codeIsActual = '<?php if(isset($tabpanel_page) && $tabpanel_page == ' .  var_export($page['name'], true) . ') echo " active"; ?>';
      $first = false;

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
      $ret[] = '<?php if(isset($tabpanel_page) && $tabpanel_page == ' .  var_export($page['name'], true) . ') { ?>';
      $ret[] = "<div class=\"tabPage\" id=\"{$page['id']}\">";
      $ret[] = "<!--\n" . var_export($page, true) . "\n-->";
      $ret[] = '<?php ' . $this->macroInclude('#' . $page['id'], '') . '; ?>';
      $ret[] = '</div>';
      $ret[] = '<?php } ?>';
    }

    // Implicit tab
    {
      $ret[] = '<?php if(!isset($tabpanel_page)) { ?>';
      $ret[] = "<div class=\"tabPage implicit\">Choose a tab</div>";
      $ret[] = '<?php } ?>';
    }
    $ret[] = '</div>';

    return implode("\n", $ret);
  }




}
