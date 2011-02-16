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

namespace Juz\Form;

use ActiveEntity\Entity;

class EntitySelectPicker extends SelectPicker {
  protected $entityName;
  protected $conditions = array();
  protected $fieldId = null; // Field used as ID
  protected $fieldName = null; // Field used as Name

  public function __construct($label = null, $entityName = null, $conditions = null) {
    parent::__construct($label, null);
    $this->entityName = $entityName;
    $this->conditions = $conditions;
  }

  public function setEntityName($entityName) {
    $this->entityName = $entityName;
  }

  public function getEntityName() {
    return $this->entityName;
  }

  public function setConditions($conditions) {
    $this->conditions = $conditions;
  }

  public function getConditions() {
    return $this->conditions;
  }

  public function addCondition($key, $val) {
    $this->conditions[$key] = $val;
    return $this;
  }

  public function addConditions($list) {
    if(!is_array($list)) throw new \InvalidArgumentException("Array expected");
    $this->conditions += $list;
    return $this;
  }

  public function getDataSource() {
    /** @var $repo \Doctrine\ORM\EntityRepository */
    $repo = Entity::getRepository($this->entityName);

    /** @var $metadata \ActiveEntity\ClassMetadata */
    $metadata = Entity::getClassMetadata($this->entityName);

    $fieldId = $this->fieldId ?: $metadata->getSingleIdentifierFieldName();
    $fieldName = $this->fieldName ?: $metadata->getNameField();

    // Load it
    $list = is_array($this->conditions) && !empty($this->conditions) ? $repo->findBy($this->conditions) : $repo->findAll();

    // Make assoc array
    $ret = array();
    foreach($list as $item) $ret[$item->$fieldId] = $item->$fieldName;

    return $ret;
  }

  public function setDataSource($dataSource) {
    throw new \InvalidStateException('Cannot change data source');
  }


  public function setFieldId($fieldId) {
    $this->fieldId = $fieldId;
  }

  public function getFieldId() {
    return $this->fieldId;
  }

  public function setFieldName($fieldName) {
    $this->fieldName = $fieldName;
  }

  public function getFieldName() {
    return $this->fieldName;
  }
}
