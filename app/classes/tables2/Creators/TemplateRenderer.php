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

namespace Juz\Tables\Creator;
use Juz\Tables\ITableStructureDefinition,
  Juz\Tables\IDataSource,
  Juz\Tables\Field;

class TemplateRenderer extends BaseRenderer implements \Juz\Tables\ITableRenderer {
  protected $templateVariables = null;

  /**
   *
   * @param array $templateVariables Map of variables to be assigned to template
   */
  public function __construct($templateVariables = null) {
    if(isset($templateVariables)) $this->templateVariables = $templateVariables;
  }

  /**
   * Renders table to standard output
   * @return void
   */
  function render() {
    $this->getTemplate()->render();
  }

  protected function getTemplatePath() {
    return \Nette\Environment::getVariable('tempDir') . '/cache/Tables/' . $this->definition->getId() . '.phtml';
  }

  /**
   * Get latte template for table
   */
  protected function getTemplate() {
    $path = $this->getTemplatePath();

    // Generate template code if needed
    if(!file_exists($path) || ($mtime = $this->definition->getMTime()) == 0 || filemtime($path) < $mtime) $this->generateTemplate($path);

    $tpl = new \Nette\Templates\FileTemplate($path);
    $tpl->registerFilter(new \Nette\Templates\LatteFilter);
    $tpl->parameters = '';
    $tpl->dataSource = $this->getDatasource();

    // Assign variables
    if($this->templateVariables) foreach($this->templateVariables as $k => $v) $tpl->$k = $v;

    return $tpl;
  }

  /**
   * Generate template code and store it to file
   * @param string $path Path to store template code
   */
  protected function generateTemplate($path) {
    @mkdir(dirname($path), 0777);

    try {
      ob_start();
      $this->generateTemplateCode();
      file_put_contents($path, ob_get_clean());
    } catch(\Exception $e) {
      ob_end_clean();
      throw new \RuntimeException('Unable to generate table template code', 1, $e);
    }
  }


  /*********** rendering ******************/

  /**
   * Generates template code
   */
  public function generateTemplateCode() {
    $nl = "\n";
    $id = $this->definition->getId(); // Table identifier

    echo "{block #content}\n";
    echo '<fieldset id="tbl-outer" class="h3 nocollapse table_outer" ajax="0" params="{$parameters}">' . $nl;
    echo ' {block #legend}<legend>' . $this->definition->getTitle() . '</legend>{/block}' . $nl;

    echo "{block #headerLinks}\n";
    $this->renderHeaderLinks();
    echo "{/block}\n\n";

    echo "{block #table}<table class=\"lines sortable withmenu\" id=\"tbl-{$id}-table\" name=\"{$id}\">\n";

    echo "<thead><tr>\n";
    $this->renderTableHeader();
    echo "</tr></thead>\n";

    echo "<tbody>\n{block #tbody}\n";
    $this->renderTableBody();
    echo "{/block}\n</tbody>\n";

    echo "</table>{/block #table}\n";

    echo '</fieldset>' . $nl;
    echo "{/block #content}\n";
  }

  protected function renderTableHeader() {
    $fields = $this->definition->getFields();

    /** @var $field \Juz\Tables\Field */
    foreach($fields as $k => $field) {
      $name = $field->name;
      $show = $field->show ? '' : 'display: none;';
      $width = $field->width;
      $title = $field->title;

      echo "  <th name=\"$name\" width=\"$width\" style=\"$show\">$title</th>\n";
    }

    // Column for item links
    if($this->definition->getFieldIndex()) echo '  <th name="_links">Actions</th>';
  }

  protected function renderTableBody() {
    /** @var $field \Juz\Tables\Field */

    $fields = $this->definition->getFields();
    $nl = "\n";

    echo '{foreach $dataSource as $item}' . $nl;
    echo '<tr index="{$item->' . $this->definition->getFieldIndex() . '}">' . $nl;

    foreach($fields as $k => $field) {
      $show = $field->show ? '' : 'display: none;';

      echo "  <td col=\"$k\" style=\"$show\">";
      $this->renderFieldContent($field);
      echo "</td>\n";
    }

    // Render links for this row
    if($links = $this->definition->getItemLinks()) {
      echo "  <td>\n";
      foreach($links as $link) {
        $this->renderLink($link);
        echo "\n";
      }
      echo "  </td>\n";
    }

    echo "</tr>\n";
    echo "{/foreach}\n";
  }

  /**
   * Render field contents
   * @param \Juz\Tables\Field $field
   * @return void
   */
  protected function renderFieldContent(Field $field) {
    if($var = $field->variable) echo '{$item->' . $var . (!empty($field->show->helper) ? '|' . $field->show->helper : '') . '}';
    elseif($content = $field->content) echo preg_replace('/(\\{[!]?\\$)([a-z0-9_]+)/i', '\\1item->\\2', $content);
  }

  protected function renderLink(\ActiveEntity\Annotations\Link $link, $fixVariableNames = true) {
    return; // TODO: dodelat
    // Prepare target
    $target = $link->module ? ":{$link->module}:" : '';
    $target .= $link->presenter . ':';
    $target .= $link->action ? "$link->action!" : $link->view;

    // Prepare parameters
    $params = array();
    foreach($link->params as $k => $p) {
      $prefix = is_int($k) ? '' : "$k => ";

      if(substr($p, 0, 1) === '$') {
        if($fixVariableNames) $params[] = $prefix . '$item->' . substr($p, 1);
        else $params[] = $prefix . $p;
      }
      else $params[] = $prefix . var_export($p, true);
    }
    $params = implode(', ', $params);

    $href = '{plink ' . $target . ($params ? ", $params" : '') . '}';
    echo '    <a href="' . $href . '" class="' . $link->class . '">' . $link->title . '</a>';
  }

  /**
   * Render links shown before the table
   * @return void
   */
  protected function renderHeaderLinks() {
    return; // TODO: dodelat
    if(!$links = $this->definition->getHeaderLinks()) return;

    foreach($links as $link) {
      $this->renderLink($link, false);
      echo "\n";
    }
  }
}