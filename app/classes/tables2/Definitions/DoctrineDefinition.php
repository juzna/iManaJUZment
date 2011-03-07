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


namespace Juz\Tables\Definition;

use Juz\Tables\ITableDefinition,
  Juz\Tables\Field,
  ActiveEntity\Annotations\Link,
  ActiveEntity\Annotations\HeaderLink,
  Doctrine;


class DoctrineDefinition extends \Nette\Object implements ITableDefinition {
  /** @var string */
  private $entityName;

  /** @var \Juz\ClassMetaData */
  private $classMetadata;

  /** @var \ActiveEntity\Metadata */
  private $aeMetadata;

  private $_cache;
  
  public function __construct($name) {
    $this->entityName = $name;
    $this->classMetadata = \ActiveEntity\Entity::getClassMetadata($name);
    $this->aeMetadata = $this->classMetadata->getExtension('ActiveEntity');
  }

  /**
   * Get unique ID of this table
   * @return string
   */
  public function getId() {
    return 'doctrine_' . $this->entityName;
  }
  
  /**
   * Get title of table
   * @return string
   */
  public function getTitle() {
    return $this->aeMetadata->getTitle();
  }

  /**
   * Get list of fields
   * @return array of TableField
   */
  public function getFields() {
    $ret = array();
    $cntVisible = 0;

    foreach($this->classMetadata->getAllFieldNames() as $fieldName) {
      $title = $this->aeMetadata->getFieldMetadata($fieldName, 'Title') ?: ucfirst($fieldName);

      // It's simple field
      if($this->classMetadata->hasField($fieldName)) {
        $definition = $this->classMetadata->getFieldMapping($fieldName);
        $ret[$fieldName] = $field = new Field($fieldName, array(
          'title'     => $title,
          'variable'  => $fieldName,
          'show'      => $show = !empty($definition['showByDefault']),
        ));
        $this->_setupTableField($field);
        if($show) $cntVisible++;
      }

      // It's association
      elseif($this->classMetadata->hasAssociation($fieldName)) {
        $definition = $this->classMetadata->getAssociationMapping($fieldName);
        if(!$this->aeMetadata->getFieldMetadata($fieldName, 'Show')) continue; // Should not be shown

        // Get index field of the other entity
        $indexField = reset($definition['sourceToTargetKeyColumns']);


        $ret[$fieldName] = $field = new Field($fieldName, array(
          'title'     => $title,
          'show'      => true,
          'contentCode' => "{= \\ActiveEntity\\Helper::DoctrineProxyIdentifier(\$item->$fieldName, '$indexField')}",
        ));
        $this->_setupTableField($field);
        $cntVisible++;
      }
    }

    // No cols have show parameter -> try to guess
    if(!$cntVisible) {
      foreach(array_keys($ret) as $i => $name) $ret[$name]->show = ($i != 0 && $i < 8) ? 1 : 0;
    }
    
    return $ret;
  }

  private function _setupTableField(Field $field) {
    // Get ActiveEntity metadata for this field
    $md = $this->aeMetadata->getFieldMetadata($field->name);
    
    if(isset($md['show'])) $field->parameters['show'] = $md['show'];
    if(isset($md['link'])) $field->parameters['link'] = array_merge(@$this->aeMetadata->classAnnotations['links'] ?: array(), $md['link']);
  }

  /**
   * Get variable which is primary key
   * @return string
   */
  public function getFieldIndex() {
    $keys = $this->aeMetadata->getFieldNames();
    return $keys[0];
  }
  
  /**
   * Get default sort column
   */
  public function getSortFields() {
    return array(
      array($this->getFieldIndex(), 0)
    );
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
  public function getItemLinks() {
    if(isset($this->_cache['links'])) return $this->_cache['links'];

    /** @var \ActiveEntity\Annotations\Links */
    $links = @$this->aeMetadata->classAnnotations['Links'];
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
          $indexField = $this->classMetadata->getSingleIdentifierFieldName();
          $linkDefinition = array(
            'title' => ucfirst($item),
            'view' => $item,
            'class' => 'in_dialog',
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
    $links = @$this->aeMetadata->classAnnotations['Links'];
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
            'class' => 'in_dialog',
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
    foreach($this->classMetadata->getAssociationMappings() as $def) {
      $fieldName = $def['fieldName'];
      if(isset($def['fieldMetadata']['ActiveEntity\\Annotations\\Required'])) $list[$fieldName] = "\$variables['$fieldName']";
    }
  }


  /**
   * Get definition of data source
   * @param string $name
   * @return IDataSourceDefinition
   */
  function getDataSourceDefinition($name = null) {
    // Create definition
    $def = new \Juz\Tables\DataSourceDefinition('doctrine-entity', array(
      'entityName' => $this->entityName,
    ));

    // Add parameters
    foreach($this->classMetadata->getAssociationMappings() as $def) {
      if(isset($def['fieldMetadata']['ActiveEntity\\Annotations\\Required'])) {
        $def->addParameter(
          new \Juz\Tables\Parameter($def['fieldName'], array('required' => true))
        );
      }
    }

    return $def;
  }
}
