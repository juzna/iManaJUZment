<?php

namespace Tables;

class SimpleRenderer extends \Nette\Object implements ITableRenderer {
  private $definition;
  private $dataSource;
  private $variables;
  
  public function __construct(ITableDefinition $def = null, \Traversable $source = null, $variables = null) {
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
  
  /**
   * Renders template and returns HTML code
   * @return string
   */
  public function render($offset = 0, $limit = 0) {
    $tpl = $this->getTemplate();
    $tpl->dataSource = $this->dataSource;
    $tpl->parameters = '';
    
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
  }
  
  protected function renderTableBody() {
    $fields = $this->definition->getFields();
    $nl = "\n";
    
    echo '{foreach $dataSource as $item}' . $nl;
    echo '<tr index="{$item->' . $this->definition->getFieldIndex() . '}">' . $nl;
    
    foreach($fields as $k => $field) {
      echo "  <td col=\"$k\">";
      echo $field->renderContent();
      echo "</td>\n";
    }
    
    echo "</tr>\n";
    echo "{/foreach}\n";
  }
}
