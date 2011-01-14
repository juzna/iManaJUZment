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
use DOMDocument, DOMElement;



class XMLTableDefinition extends \Nette\Object implements ITableDefinition {
  private $file;
  private $loaded = false;

  private $title = '';
  private $params = array();
  private $source;
  private $attributes = array();
  private $fields = array();
  private $fieldIndex;
  private $sort;
  private $container = null;
  
  
  /**
   * @param string $file Path to definition
   */
  public function __construct($file) {
    if(!is_file($this->file = $file)) throw new \InvalidArgumentException("File not exists");
  }
  
  
  /************** XML parser *************/
  
  /**
  * Loads XML definition
  */
  public function load() {
    if($this->loaded) return;
    
    // Loads XML document from file
    $doc = new DOMDocument();
    $doc->load($this->file);
    
    // Parse XML document itself
    $this->loadXMLDocument($doc);
    
    $this->validateDefinition();
    
    $this->loaded = true;
  }
  
  /**
   * Validate loaded definition, throw exception if invalid
   */
  protected function validateDefinition() {
  
  }
  
  /**
   * Loads template table first
   * @param string $name Name of template to be loaded
   */
  protected function loadTemplate($name) {
    $doc = new DOMDocument();
    
    // Generate path
    $path = dirname($this->file) . '/' . $name;
    if(substr($path, -4) !== '.xml') $path .= '.xml';
    
    if(realname($path) == realname($this->file)) throw new \Exception("Table definition is trying to load itself as template");
    
    $doc->load($path);
    
    $this->loadXMLDocument($doc);
  }
  
  /**
   * Parse XML document itself
   */
  protected function loadXMLDocument(DOMDocument $doc) {
    $table = $doc->documentElement;
    
    // Load template first
    if($template = $table->getAttribute('template')) $this->loadTemplate($template);
    
    // Get attributes
    $this->attributes = self::getElementAttributes($table);
    
    // Parse table elements
    for($i = 0; $i < $table->childNodes->length; $i++) {
      $el = $table->childNodes->item($i);
      if(!($el instanceof DOMElement)) continue;
      
      // Call loading function for that element
      $f = 'loadElement' . ucfirst($el->tagName);
      if(method_exists($this, $f)) $this->$f($el);
      else user_error("Unknown element '$el->tagName'");
    }
  }
  
  protected function loadElementContainer($el) {
    $this->container = self::XMLDump($el);
  }
  
  protected function loadElementTitle($el) {
    $this->title = self::XMLDump($el);
  }
  
  protected function loadElementParameters($el) {
    for($i = 0; $i < $el->childNodes->length; $i++) {
      $param = $el->childNodes->item($i);
      if(!($param instanceof DOMElement)) continue;
      if($param->tagName != 'param') throw new TableException("Expected param element, got $param->tagName");
      
      // Get attributes
      $name = $param->getAttribute('name');
      $attributes = self::getElementAttributes($param);
      
      // Create parameter
      $this->params[$name] = new TableParameter($name, $attributes);
    }
  }
  
  protected function loadElementSource($el) {
    if(!$type = $el->getAttribute('type')) throw new TableException("Data source type not set");
    
    $this->source = self::getElementAttributes($el);
    $this->source['type'] = $type;
    $this->source['value'] = self::XMLDump($el);
  }
  
  /**
   * Loads list of fields
   */
  protected function loadElementFields($container) {
    // Set index field
    if(($index = $container->getAttribute('index')) !== null) $this->fieldIndex = $index;
    if($sort = $container->getAttribute('sort')) $this->sort = $sort;
  
    for($i = 0; $i < $container->childNodes->length; $i++) {
      $el = $container->childNodes->item($i);
      if(!($el instanceof DOMElement)) continue;
      if($el->tagName != 'field') throw new TableException("Expected field element, got $el->tagName");
      
      // Get attributes
      $attributes = self::getElementAttributes($el);
      if(empty($attributes['name'])) {
        if(!empty($attributes['variable'])) $attributes['name'] = $attributes['variable'];
        else throw new TableException("Field not having name attribute");
      }
      $attributes['show'] = (!isset($attributes['show']) || $attributes['show']) ? 1 : 0;
      
      // Create new field object
      $name = $attributes['name'];
      $this->fields[$name] = $field = new TableField($name, $attributes);
      
      
      // Proccess child nodes
      if($el->hasChildNodes()) {
        for($j = 0; $j < $el->childNodes->length; $j++) {
          $child = $el->childNodes->item($j);
          
          if($child instanceof DOMElement) switch(strtolower($child->tagName)) {             
            // Content
            case 'content':
              $field->contentCode = self::XMLDump($child);
              break;
              
            case 'filter':
              break;
              
              
            default:
              throw new TableException("Unkown child $child->tagName in field element");
          }
        }
      }
    }
  }
  
  
  
  /*************** general methods ******************/
 
  /**
   * Dump XML data of an element
   */
  public static function XMLDump($el) {
    $code = $el->ownerDocument->saveXML($el);
    $len = strlen($el->tagName);
    
    $ret = trim(substr($code, strpos($code, '>') + 1, - $len - 3));
    if(substr($ret, 0, 9) == '<![CDATA[') $ret = substr($ret, 9, -3);
    
    $ret = htmlspecialchars_decode($ret);
    
    return trim($ret);
  }
  
  /**
   * Get associative array of attributes and their values
   * @return array
   */
  public static function getElementAttributes($el) {
    $ret = array();
    foreach($el->attributes as $attr => $value) $ret[$attr] = $value->nodeValue;
    return $ret;
  }
  
  
  
  
 
  /*********** public methods **********/
  
  public function getId() {
    return sha1($this->file);
  }
  
  public function getName() {
    $this->load();
    return $this->attributes['name'];
  }   
 
  /**
   * Get title of table
   * @return string
   */
  public function getTitle() {
    $this->load();
    return $this->title;
  }
  
  /**
   * Get list of parameters, which the tables requests
   * @return array of TableParameter
   */
  public function getParameters() {
    $this->load();
    return $this->params;
  }
  
  /**
   * Get data source description
   * @return array Associative array of parameters, type tells which data source should be used
   */
  public function getDataSource() {
    $this->load();
    return $this->source;
  }
  
  /**
   * Get list of fields
   * @return array of TableField
   */
  public function getFields() {
    $this->load();
    return $this->fields;
  }
  
  /**
   * Get variable which is primary key
   * @return string
   */
  public function getFieldIndex() {
    $this->load();
    return $this->fieldIndex;
  }
  
  /**
   * Get default sort column
   */
  public function getSortField() {
    $this->load();
    return $this->sort;
  }
  
  /**
   * Get HTML code of container template
   */
  public function getContainer() {
    $this->load();
    return $this->container;
  }
  
  /**
   * Get last change of template definition (for cache invalidation)
   * @return int timesamp
   */
  public function getMTime() {
    return filemtime($this->file);
  }
}
