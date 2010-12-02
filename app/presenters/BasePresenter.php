<?php

use Nette\Application\Presenter,
  Nette\Environment;


/**
 * Base class for all application presenters.
 */
abstract class BasePresenter extends Presenter {
  // Parameters for creating templates
  protected $_templateFactory = 'default';
  protected $_templateFactoryParams = null;
  
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
	
	/************ Template factory ****************/
	
	/**
	 * Configure template factory
	 * @param string $name Name of factory: default, code, or any method createTemplate<name>
	 * @param mixed $params Whatever parameters, which are passed to factory
	 */
	protected function setTemplateFactory($name, $params = null) {
	  $this->_templateFactory = $name;
	  $this->_templateFactoryParams = $params;
  }
	
	/**
	 * Create template based on how factory's configuration
	 * @see setTemplateFactory
	 * @return Nette\Templates\ITemplate
	 */
	protected function createTemplate() {
	  switch($this->_templateFactory) {
	    case 'default':
	    case '':
  	    return parent::createTemplate();
  	    
	    case 'code':
	      return new \CodeTemplate(parent::createTemplate());
	      
      default:
        $method = 'createTemplate' . ucfirst($this->_templateFactory);
        if(method_exists($this, $method)) return $this->$method($this->_templateFactoryParams);
        throw new \Exception("Invalid template factory: '$this->_templateFactory'");
	  }
  }
	
	
	/******************** Tables *********************/
  
  /**
   * Get table renderer
   * @param string $name File name of table definition
   * @param array $variables Map of variables to be used for generating the table
   * @param string $renderer Name of renderer class (if not passed, will use SimpleRenderer)
   * @param Traversable $ds Data source to be displayed (if not passed, will be taken from definition)
   * @return Tables\ITableRenderer
   */
  public function getTable($name, $variables = null, $renderer = null, $ds = null) {
    // Try to look for XML definition
    $def = $this->getTableDefinitionFromXML($name) or
      $def = $this->getTableDefinitionFromModel($name);
    if(!$def) throw new \Exception("Table '$name' not found");
    
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
  
  /**
   * Gets table definition loaded from XML file
   * @param string $name Table definition file
   * @return Tables\ITableDefinition
   */
  protected function getTableDefinitionFromXML($name) {
    $path = $this->getModulePath('tables') . '/' . $name;
    if(substr($name, -4) != '.xml') $path .= '.xml';
    if(!is_readable($path)) return false;
    
    // Load definition    
    $def = new Tables\XMLTableDefinition($path);
    $def->load();
    
    return $def;
  }
  
  /**
   * Get table definition based on Doctrine 2 model
   * @param string $modelName Class name of model
   * @return Tables\ITableDefinition
   */
  protected function getTableDefinitionFromModel($modelName) {
    if(!class_exists($modelName)) return false;
    
    $reflClass = new \ReflectionClass($modelName);
    if(!$reflClass->isSubclassOf('ActiveEntity\Entity')) return false;
    
    return new \Tables\DoctrineEntityTableDefinition($modelName);
  }
}

