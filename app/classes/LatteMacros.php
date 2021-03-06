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

    $this->macros['table'] = "%:macroTable%";
    $this->macros['tabpanel'] = "%:macroTabPanel%";
    $this->macros['/tabpanel'] = "%:macroTabPanelEnd%";
    $this->macros['tabpage'] = "%:macroTabPage%";
    $this->macros['/tabpage'] = "%:macroTabPageEnd%";
    $this->macros['backlink'] = "<?php echo %:escape%(%:macroBacklink%); ?>";
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

  /**
   * Draws a table or a tab-page with a table
   */
  public function macroTable($content, $modifiers) {
    $tableName = $this->fetchToken($content);
    $ds = $this->fetchToken($content);

    $codeTable = '<?php $presenter->drawTable(' . $this->formatString($tableName) . ', ' . $ds . $this->formatArray($content, ', ') . '); ?>';

    // We're in TabPanel, but not in page -> this table is another TabPage
    if(isset($this->tabPanelStack[0]['.inPage']) && !$this->tabPanelStack[0]['.inPage']) {
      return $this->macroTabPage($tableName, '') . $codeTable . $this->macroTabPageEnd('', '');
    }

    // Just a table
    else {
      return $codeTable;
    }
  }

  /**
   * Generate link with backlink argument
   * @return string
   */
  public function macroBacklink($content, $modifiers) {
    return $this->formatModifiers('$presenter->link_back(' . $this->formatLink($content) .')', $modifiers);
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
  }

  // End of tab-panel
  public function macroTabPanelEnd($content, $modifiers) {
    // Remove this tab-panel from stack
    $tabPanel = array_shift($this->tabPanelStack);

    return LayoutFactory::getLayout(\Nette\Environment::getHttpRequest())->renderComponent('tabpanel', $tabPanel, array(
      'macros' => $this,
    ));
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
    $tabPanel['.inPage'] = true;
    $this->namedBlocks[$blockName] = $blockName;
    return "<?php {block $blockName} ?>";
  }

  // End of a page
  public function macroTabPageEnd($content, $modifiers) {
    $this->tabPanelStack[0]['.inPage'] = false;
    $blockName = $this->tabPanelStack[0]['.lastBlockName'];
    return "<?php {/block $blockName} ?>";
  }
}
