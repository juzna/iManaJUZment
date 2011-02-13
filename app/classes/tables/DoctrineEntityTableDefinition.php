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
use Doctrine,
  \ActiveEntity\Annotations\Link,
  \ActiveEntity\Annotations\HeaderLink;


class DoctrineEntityTableDefinition extends \Nette\Object implements ITableDefinition {
  /** @var string */
  private $entity;

  /** @var ActiveEntity\ClassMetadata */
  private $metadata;

  private $_cache;
  
  public function __construct($name) {
    $this->entity = $name;
    $this->metadata = \ActiveEntity\Entity::getClassMetadata($name);
  }

  /**
   * Get unique ID of this table
   * @return string
   */
  public function getId() {
    return $this->getName();
  }
  
  /**
   * Get name which will be useable in JS
   * @return string
   */
  public function getName() {
    return "doctrine-$this->entity";
  }

  /**
   * Get title of table
   * @return string
   */
  public function getTitle() {
    return $this->metadata->getTitle();
  }
  
  /**
   * Get list of parameters, which the tables requests
   * @return array of TableParameter
   */
  public function getParameters() {
    $ret = array();
    foreach($this->metadata->getAssociationMappings() as $def) {
      if(isset($def['fieldMetadata']['ActiveEntity\\Annotations\\Required'])) {
        $ret[] = new TableParameter($def['fieldName'], array('required' => true));
      }
    }

    return $ret;
  }
  
  /**
   * Get data source description
   * @return array Associative array of parameters, type tells which data source should be used
   */
  public function getDataSource() {
    return array(
      'type'  => 'd:table',
      'value' => $this->entity,
    );
  }
  
  /**
   * Get list of fields
   * @return array of TableField
   */
  public function getFields() {
    $ret = array();
    $cntVisible = 0;

    foreach($this->metadata->getAllFieldNames() as $fieldName) {
      // It's simple field
      if($this->metadata->hasField($fieldName)) {
        $definition = $this->metadata->getFieldMapping($fieldName);
        $ret[$fieldName] = $field = new TableField($fieldName, array(
          'title'     => isset($definition['title']) ? $definition['title'] : ucfirst($fieldName),
          'variable'  => $fieldName,
          'show'      => $show = !empty($definition['showByDefault']),
        ));
        $this->_setupTableFieldFromMetadata($field, $definition);
        if($show) $cntVisible++;
      }

      // It's association
      elseif($this->metadata->hasAssociation($fieldName)) {
        $definition = $this->metadata->getAssociationMapping($fieldName);
        if(empty($definition['fieldMetadata']['ActiveEntity\\Annotations\\Show'])) continue; // Should not be shown

        // Get index field of the other entity
        $indexField = reset($definition['sourceToTargetKeyColumns']);

        $ret[$fieldName] = $field = new TableField($fieldName, array(
          'title'     => isset($definition['title']) ? $definition['title'] : ucfirst($fieldName),
          'show'      => true,
          'contentCode' => "{= \\ActiveEntity\\Helper::DoctrineProxyIdentifier(\$item->$fieldName, '$indexField')}",
        ));
        $this->_setupTableFieldFromMetadata($field, $definition);
        $cntVisible++;
      }
    }

    // No cols have show parameter -> try to guess
    if(!$cntVisible) {
      foreach(array_keys($ret) as $i => $name) $ret[$name]->show = ($i != 0 && $i < 8) ? 1 : 0;
    }
    
    return $ret;
  }

  /**
   * Sets-up parameters of table field based on it's metadata
   */
  protected function _setupTableFieldFromMetadata(TableField $field, array $def) {
    if(!$md = @$def['fieldMetadata']) return;

    if($link = @$md['ActiveEntity\\Annotations\\Link']) $field->parameters['link'] = $this->mergeMetadata($this->metadata->classMetadata['ActiveEntity\\Annotations\\Links'], $link);
    if($p = @$md['ActiveEntity\\Annotations\\Show']) $field->parameters['show'] = $p;
  }

  protected function mergeMetadata($default, $md) {
    if($default) foreach($default as $k => $v) {
      if($k !== 'value' && property_exists($md, $k) && !isset($md->$k)) $md->$k = $v;
    }
    return $md;
  }

  
  /**
   * Get variable which is primary key
   * @return string
   */
  public function getFieldIndex() {
    $keys = $this->metadata->getFieldNames();
    return $keys[0];
  }
  
  /**
   * Get default sort column
   */
  public function getSortField() {
    return $this->getFieldIndex();
  }
  
  /**
   * Get HTML code of container template
   */
  public function getContainer() {
    return null;
  }
  
  /**
   * Get last change of template definition (for cache invalidation)
   * @return int timesamp
   */
  public function getMTime() {
    return 0;
  }

  /**
   * Get list of links for each row
   * @return array [ { title, module, presenter, view?, action?, params[] } ]
   */
  public function getLinks() {
    if(isset($this->_cache['links'])) return $this->_cache['links'];

    /** @var ActiveEntity\Annotations\Links */
    $links = @$this->metadata->classMetadata['ActiveEntity\\Annotations\\Links'];
    if(!$links) return;

    $ret = array();

    // Prepare common links
    if(is_array($links->common)) foreach($links->common as $item) {
      $linkDefinition = null;
      switch($item) {
        case 'detail':
        case 'edit':
        case 'clone':
        case 'remove':
        case 'delete':
          $indexField = $this->metadata->getSingleIdentifierFieldName();
          $linkDefinition = array(
            'title' => ucfirst($item),
            'view' => $item,
            'params' => array(
              $links->alias,
              '$' . $indexField,
            )
          );
          break;

        case 'add':
          // Nothing ;)
      }

      // Add new link
      if($linkDefinition) {
        foreach(array('module', 'presenter', 'view', 'action', 'params') as $f) if(!isset($linkDefinition[$f]) && isset($links->$f)) $linkDefinition[$f] = $links->$f;
        $ret[] = new Link($linkDefinition);
      }
    }


    // Prepare explicit links
    if(is_array($links->value)) foreach($links->value as $link) {
      if($link instanceof Link) {
        // Get default values
        foreach(array('module', 'presenter', 'view', 'action', 'params') as $f) if(!isset($link->$f) && isset($links->$f)) $link->$f = $links->$f;

        $ret[] = $link;
      }
    }

    return $this->_cache['links'] = $ret;
  }

  /**
   * Get list of links which should be shown in header
   * @return array [ { title, module, presenter, view?, action?, params[] } ]
   */
  public function getHeaderLinks() {
    if(isset($this->_cache['headerLinks'])) return $this->_cache['headerLinks'];

    /** @var ActiveEntity\Annotations\Links */
    $links = @$this->metadata->classMetadata['ActiveEntity\\Annotations\\Links'];
    if(!$links) return;

    $ret = array();

    // Prepare common links
    if(is_array($links->common)) foreach($links->common as $item) {
      $linkDefinition = null;
      switch($item) {
        case 'detail':
        case 'edit':
        case 'clone':
        case 'remove':
        case 'delete':
          // Not for this section
          break;

        case 'add':
          $linkDefinition = array(
            'title' => ucfirst($item),
            'view' => $item,
            'params' => array(
              $links->alias,
            )
          );
          $this->_getMoreParamsForAddLink($linkDefinition['params']);
          break;
      }

      // Add new link
      if($linkDefinition) {
        foreach(array('module', 'presenter', 'view', 'action', 'params') as $f) if(!isset($linkDefinition[$f]) && isset($links->$f)) $linkDefinition[$f] = $links->$f;
        $ret[] = new HeaderLink($linkDefinition);
      }
    }


    // Prepare explicit links
    if(is_array($links->value)) foreach($links->value as $link) {
      if($link instanceof HeaderLink) {
        // Get default values
        foreach(array('module', 'presenter', 'view', 'action', 'params') as $f) if(!isset($link->$f) && isset($links->$f)) $link->$f = $links->$f;

        $ret[] = $link;
      }
    }

    return $this->_cache['headerLinks'] = $ret;
  }

  private function _getMoreParamsForAddLink(&$list) {
    foreach($this->metadata->getAssociationMappings() as $def) {
      $fieldName = $def['fieldName'];
      if(isset($def['fieldMetadata']['ActiveEntity\\Annotations\\Required'])) $list[$fieldName] = "\$variables['$fieldName']";
    }
  }
}
