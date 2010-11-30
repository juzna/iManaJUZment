<?php

use Nette\Application\Presenter,
  Nette\Environment;


/**
 * Base class for all application presenters.
 */
abstract class BasePresenter extends Presenter
{
  /**
   * Get specific path for actual module
   * @param string $dir Requested subdirectory
   * @return string
   */
  public function getModulePath($dir = null) {
		$appDir = Environment::getVariable('appDir');
		$path = '/' . str_replace(':', 'Module/', $this->getName());
		$path = substr($path, 0, strrpos($path, '/') + 1);
		
		return realpath($appDir . '/' . $path . ($dir ? "$dir/" : ''));
	}
  
  /**
   * Get table renderer
   * @param string $name File name of table definition
   * @param array $variables Map of variables to be used for generating the table
   * @param string $renderer Name of renderer class (if not passed, will use SimpleRenderer)
   * @param Traversable $ds Data source to be displayed (if not passed, will be taken from definition)
   * @return Tables\ITableRenderer
   */
  public function getTable($name, $variables = null, $renderer = null, $ds = null) {
    $path = $this->getModulePath('tables') . '/' . $name;
    if(substr($name, -4) != '.xml') $path .= '.xml';
    if(!is_readable($path)) throw new Exception("Table not found: '$path'");
    
    // Load definition    
    $def = new Tables\XMLTableDefinition($path);
    $def->load();
    
    // Get data source
    if(empty($ds)) $ds = Tables\DataSourceFactory::fromTableDefinition($def);
    elseif(!($ds instanceof Traversable)) throw new Exception("Data source is not Traversable");
    
    // Prepare renderer
    if(empty($renderer)) $renderer = 'SimpleRenderer';
    if(is_string($renderer)) {
      if(class_exists($className = "\\Tables\\$renderer") || class_exists($className = $renderer)) $renderer = new $className($def, $ds);
      else throw new Exception("Renderer class not found");
    }
    if(!($renderer instanceof Tables\ITableRenderer)) throw new Exception("Renderer is not valid");
    
    // Set renderer variables
    if(!empty($variables)) $renderer->setVariables($variables);
    
    // Debug: dump renderer's template
    echo "<!---- TEMPLATE:\n\n";
    $renderer->generateTemplateCode();
    echo "\n\n-->\n";
    
    return $renderer;
  }
}

