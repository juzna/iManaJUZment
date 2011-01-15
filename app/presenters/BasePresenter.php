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


use Nette\Application\Presenter,
  Nette\Environment;


/**
 * Base class for all application presenters.
 */
abstract class BasePresenter extends Presenter {
  protected $public = false;

  // Parameters for creating templates
  protected $_templateFactory = 'default';
  protected $_templateFactoryParams = null;

  function startup() {
    parent::startup();

		// user authentication
		if(!$this->public && !$this->user->isLoggedIn()) {
			if ($this->user->logoutReason === Nette\Web\User::INACTIVITY) {
				$this->flashMessage('You have been signed out due to inactivity. Please sign in again.');
			}
			$backlink = $this->application->storeRequest();
			$this->redirect(':Base:Sign:welcome', array('backlink' => $backlink));
		}
  }

  /**
   * Prepare file name for template, allows some custom paths
   * @param  $presenter
   * @param  $view
   * @return array of strings
   */
  public function formatTemplateFiles($presenter, $view) {
    // Get basic files
    $files = parent::formatTemplateFiles($presenter, $view);

    // Add some more
    $appDir = Environment::getVariable('appDir');

    $files[] = "$appDir/templates/common/$view.latte";
    $files[] = "$appDir/templates/common/$view.phtml";

    return $files;
  }
  
  /**
   * Get specific path for actual module
   * @param string $dir Requested subdirectory
   * @return string
   */
  public function getModulePath($dir = null) {
		$appDir = Environment::getVariable('appDir');
		$path = '/' . str_replace(':', 'Module/', $this->getName());
		$path = substr($path, 0, strrpos($path, '/') + 1);
		
		return realpath($appDir . '/' . $path . '/' . ($dir ? "$dir/" : ''));
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
  	    $template = parent::createTemplate();
        $template->registerHelperLoader('ActiveEntity\\Helper::loader'); // Add helpers
        return $template;

	    case 'code':
	      return new \CodeTemplate(parent::createTemplate());
	      
      default:
        $method = 'createTemplate' . ucfirst($this->_templateFactory);
        if(method_exists($this, $method)) return $this->$method($this->_templateFactoryParams);
        throw new \Exception("Invalid template factory: '$this->_templateFactory'");
	  }
  }

  /**
   * Set-up basic latte filters and add own specific macros
   */
  public function templatePrepareFilters($template) {
    $latte = new Nette\Templates\LatteFilter;
    $latte->setHandler(new \LatteMacros()); // Set my extended macros
    $template->registerFilter($latte);
  }

  /**
   * Generate link with backlink parameter
   * @param string $destination
   * @param array $args
   * @return string
   */
  public function link_back($destination, $args = array()) {
    // Prepare backlink
    static $backlink = null;
    if(!isset($backlink)) $backlink = $this->application->storeRequest();
    
    // Create link
    if(!is_array($args)) $args = (array) $args;
    $args['backlink'] = $backlink;
    return $this->link($destination, $args);
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
    /** @var \Tables\ITableDefinition */
    $def = $this->getTableDefinitionFromXML($name) or
      $def = $this->getTableDefinitionFromModel($name);
    if(!$def) throw new \Exception("Table '$name' not found");

    // Check, if we got all variables
    foreach($def->getParameters() as $param) {
      if($param->required && !isset($variables[$param->name])) throw new \InvalidArgumentException("Needed parameter $param->name, but not given");
    }
    
    // Get data source
    if(is_array($ds)) $ds = new \ArrayIterator($ds);
    if(empty($ds)) $ds = Tables\DataSourceFactory::fromTableDefinition($def, $variables);
    elseif(!($ds instanceof Traversable)) throw new Exception("Data source is not Traversable");
    
    // Prepare renderer
    if(empty($renderer)) $renderer = 'SimpleRenderer';
    if(is_string($renderer)) {
      if(class_exists($className = "\\Tables\\$renderer") || class_exists($className = $renderer)) $renderer = new $className($def, $ds);
      else throw new Exception("Renderer class not found");
    }
    if(!($renderer instanceof Tables\ITableRenderer)) throw new Exception("Renderer is not valid");
    $renderer->setPresenter($this);;
    
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

  public function drawTable($name, $ds, $variables = null) {
    echo $this->getTable($name, $variables, null, $ds)->render();
  }

  /*********  Entity work *******/

  /**
   * @throws InvalidArgumentException
   * @param string $alias Alias of entity in module
   * @return string 
   */
  protected function getEntityName($alias) {
    if(isset($this->entityAliases[$alias])) return $this->entityAliases[$alias];
    else throw new \InvalidArgumentException("Entity alias '$alias' not found");
  }
  
}

