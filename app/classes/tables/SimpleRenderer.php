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


namespace Tables;

class SimpleRenderer extends \Nette\Object implements ITableRenderer {
  /** @var ITableDefinition */
  private $definition;
  private $dataSource;
  private $variables;
  private $presenter;

  /**
   * Simple renderer for tables, which creates HTML layout
   * @param ITabledefinition $def Table definition
   * @param Traversable $source Data source, array or Traversable class
   * @param array $variables Variables for creating datasource
   */
  public function __construct(ITableDefinition $def = null, $source = null, $variables = null) {
    $this->definition = $def;
    $this->dataSource = $source;
    $this->variables = (array) $variables;
  }
  
  public function getDefinition() {
    return $this->definition;
  }
  
  public function setDefinition(ITableDefinition $def) {
    $this->definition = $def;
    return $this;
  }
  
  public function getDataSource() {
    return $this->dataSource;
  }
  
  public function setDataSource(\Traversable $source) {
    $this->dataSource = $source;
    return $this;
  }
  
  public function getVariablesNeeded() {
    return $this->definition->getParameters();
  }
  
  public function getVariables() {
    return $this->variables;
  }
  
  public function setVariables(array $arr) {
    foreach($arr as $k => $v) $this->variables[$k] = $v;
  }

  public function setPresenter(\Nette\Application\Presenter $presenter) {
    $this->presenter = $presenter;
    return $this;
  }

  /**
   * @return Nette\Application\Presenter
   */
  public function getPresenter() {
    return $this->presenter;
  }
  
  /**
   * Renders template and returns HTML code
   * @return string
   */
  public function render($offset = 0, $limit = 0) {
    // Create data source if needed
    if(empty($this->dataSource)) $this->dataSource = DataSourceFactory::fromTableDefinition($this->definition, $this->variables);
  
    $tpl = $this->getTemplate();
    $tpl->dataSource = $this->dataSource;
    $tpl->parameters = '';
    $tpl->presenter = $this->presenter;
    $tpl->variables = $this->variables;
    
    return $tpl->__toString();
  }
  
  /**
   * Get template for table
   */
  protected function getTemplate() {
    $path = \Nette\Environment::getVariable('tempDir') . '/cache/Tables/' . $this->definition->getId() . '.tpl';
    
    // Generate template code if needed
    if(!file_exists($path) || ($mtime = $this->definition->getMTime()) == 0 || filemtime($path) < $mtime) $this->generateTemplate($path);
    
    $tpl = new \Nette\Templates\FileTemplate($path);
    $tpl->registerFilter(new \Nette\Templates\LatteFilter);
    
    return $tpl;
  }
  
  /**
   * Generate template
   */
  protected function generateTemplate($path) {
    @mkdir(dirname($path), 0777);
    
    try {
      ob_start();
      $this->generateTemplateCode();
      file_put_contents($path, ob_get_contents());
      ob_end_clean();
    } catch(Exception $e) {
      ob_end_clean();
      throw new TableException('Unable to generate table template code', 1, $e);
    }
  }
  
  
  /*********** renreding ******************/
  
  /**
   * Generates template code
   */
  public function generateTemplateCode() {
    $nl = "\n";
    
    echo "{block #content}\n";
    echo '<fieldset id="tbl-outer" class="h3 nocollapse table_outer" ajax="0" params="{$parameters}">' . $nl;
    echo ' {block #legend}<legend>' . $this->definition->getTitle() . '</legend>{/block}' . $nl;

    echo "{block #headerLinks}\n";
    $this->renderHeaderLinks();
    echo "{/block}\n\n";

    $name = $this->definition->getName();
    echo "{block #table}<table class=\"lines sortable withmenu\" id=\"tbl-{$name}-table\" name=\"{$name}\">\n";
    
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
    
    foreach($fields as $k => $field) {
      $name = $field->name;
      $show = $field->show ? '' : 'display: none;';
      $width = $field->width;
      $title = $field->title;
      
      echo "  <th name=\"$name\" width=\"$width\" style=\"$show\">$title</th>\n";
    }

    // Col for links
    if($this->definition->getLinks()) echo '  <th name="_links">Akce</th>';
  }
  
  protected function renderTableBody() {
    $fields = $this->definition->getFields();
    $nl = "\n";
    
    echo '{foreach $dataSource as $item}' . $nl;
    echo '<tr index="{$item->' . $this->definition->getFieldIndex() . '}">' . $nl;
    
    foreach($fields as $k => $field) {
      $show = $field->show ? '' : 'display: none;';
      
      echo "  <td col=\"$k\" style=\"$show\">";
      $field->renderContent();
      echo "</td>\n";
    }

    // Render links for this row
    if($links = $this->definition->getLinks()) {
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

  protected function renderLink(\ActiveEntity\Annotations\Link $link, $fixVariableNames = true) {
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
    echo '    <a href="' . $href . '">' . $link->title . '</a>';
  }

  /**
   * Render links shown before the table
   * @return void
   */
  protected function renderHeaderLinks() {
    if(!$links = $this->definition->getHeaderLinks()) return;
    
    foreach($links as $link) {
      $this->renderLink($link, false);
      echo "\n";
    }    
  }
}
