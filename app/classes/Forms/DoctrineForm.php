<?php

use Nette\Application\AppForm;

class DoctrineForm extends AppForm {
  /**
   * @var ClassMetadata
   */
  private $metadata;
  private $entityName;
  
  /**
  * Application form constructor.
  */
  public function __construct($entity = null, Nette\IComponentContainer $parent = NULL, $name = NULL) {
    parent::__construct($parent, $name);
    if($entity) $this->setEntity($entity);
  }
  
  public function setEntity($name) {
    $this->entityName = $name;
    $this->metadata = \ActiveEntity\Entity::getClassMetadata($name);

    $this->_initFields();
  }
  
  /**
  * Init all fields from metadata
  */
  protected function _initFields() {
    $this->addHidden('action'); // Action to be done
    $this->addHidden('index');  // Index of an item (for edit, clone or delete)

    // Add all fields
    foreach($this->metadata->getFieldDefinitions() as $def) {
      switch($def['type']) {
        case 'string':
        default:
          $this->addText($def['fieldName'], $def['fieldName']);
          break;
      }
    }
  }

  public function saveForm() {
    switch($action = $this['action']->getValue()) {
      case 'add': return $this->saveAdd();
      case 'edit': return $this->saveEdit();
      case 'clone': return $this->saveClone();
      default: throw new \Nette\Application\BadRequestException("Invalid action for saving the form: '$action'");
    }
  }

  public function saveAdd($values = null) {
    if(!$values) $values = $this->getValues();

    $cls = $this->entityName;
    $obj = new $cls;

    $this->_setEntityValues($obj, $values);

    $obj->persist();
    $obj->flush();
  }

  public function saveClone($values = null) {
    if(!$values) $values = $this->getValues();

    $cls = $this->entityName;
    if(method_exists($cls, 'clone')) {
      $orig = \ActiveEntity\Entity::find($values['index'], $cls);
      $cls = $orig->clone();
    }
    else {
      $obj = new $cls;
    }

    // Set values
    $this->_setEntityValues($obj, $values);

    $obj->persist();
    $obj->flush();
  }

  /**
   * Set values of an entity from form, it uses metadata to discover type
   * @param Entity $obj
   * @param array $values
   * @return void
   */
  protected function _setEntityValues($obj, $values) {
    // Update values
    foreach($this->metadata->getFieldDefinitions() as $def) {
      $fieldName = $def['fieldName'];
      if(!array_key_exists($fieldName, $values)) continue;
      $value = $values[$fieldName];

      switch($def['type']) {
        case 'string':
        default:
          $obj->$fieldName = $value;
          break;

        case 'number':
        case 'int':
        case 'integer':
          if($value === '' || $value === null) $obj->$fieldName = null;
          else $obj->$fieldName = (int) $value;
          break;

        case 'date':
        case 'datetime':
          if(empty($value)) $obj->$fieldName = null;
          else $obj->$fieldName = new \DateTime($value);
          break;

        case 'bool':
        case 'boolean':
          if($value === '' || $value === null) $obj->$fieldName = null;
          else $obj->$fieldName = in_array(strtolower($value), array('1', 'y', 'yes', 'on'));
          break;
      }
    }
  }
  
  
  
}
