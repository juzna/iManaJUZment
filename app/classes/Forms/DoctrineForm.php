<?php

use Nette\Application\AppForm;

class DoctrineForm extends AppForm {
  public static $metadataNamespaces = array(
    'Doctrine\ORM\Mapping'     => '',
    'ActiveEntity\Annotations' => 'ae',
  );

  /**
   * @var \ActiveEntity\ClassMetadata
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

  /**
   * Set entity name for this form
   * @param string $name
   * @return void
   */
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

    foreach($this->metadata->getAllFieldNames() as $fieldName) {
      $el = $description = null;

      // It's simple field
      if($this->metadata->hasField($fieldName)) {
        $def = $this->metadata->getFieldMapping($fieldName);
        if(!$this->isFieldEditable($def)) continue;
        $label = $this->_getLabel($def, $description);
        $fieldName = $def['fieldName'];

        switch($def['type']) {
          case 'string':
          default:
            $el = $this->addText($fieldName, $label);
            break;

          case 'boolean':
            $el = $this->addCheckBox($fieldName, $label);
            break;
        }
      }

      // It's mapping
      elseif($this->metadata->hasAssociation($fieldName)) {
        $def = $this->metadata->getAssociationMapping($fieldName);
        if(isset($def['fieldMetadata']['ActiveEntity\\Annotations\\Required'])) {
          $this->addHidden($fieldName);
        }
      }

      // Set description
      if($el && !empty($description)) $el->setDescription($description);
    }
  }

  /**
   * Get label for a field
   * @param array $def Definition of a field
   * @return string
   */
  protected function _getLabel($def, &$description = null) {
    $md = $this->_getFieldMetadata($def);

    $description = isset($md['ae:description']) ? $md['ae:description']->value : ''; 

    if(isset($md['ae:title']) && $md['ae:title']->value) return $md['ae:title']->value;
    else return ucfirst(preg_replace('/[-_]/', ' ', String::uncamelize($def['fieldName']))) . ($def['type'] == 'boolean' ? '?' : '');
  }

  /**
   * Get metadata of a field while translating namespaces
   * @param array $def
   * @return array
   */
  protected function _getFieldMetadata($def) {
    if(empty($def['fieldMetadata'])) return array();
    $ret = array();
    foreach($def['fieldMetadata'] as $key => $val) {
      if($pos = strrpos($key, '\\')) {
        $ns = substr($key, 0, $pos);
        $name = lcfirst(substr($key, $pos + 1));

        if(isset(self::$metadataNamespaces[$ns])) {
          $ns = self::$metadataNamespaces[$ns];
          $ret[$ns ? "$ns:$name" : $name] = $val;
        }
      }
    }
    return $ret;
  }




  /************  Saving form  *****************/

  /**
   * General action for saving this form, which decides what to do next
   * @return void
   */
  public function saveForm() {
    switch($action = $this['action']->getValue()) {
      case 'add': return $this->saveAdd();
      case 'edit': return $this->saveEdit();
      case 'clone': return $this->saveClone();
      default: throw new \Nette\Application\BadRequestException("Invalid action for saving the form: '$action'");
    }
  }

  /**
   * Saved form which add new entity
   * @param array $values Values to be saved
   * @return void
   */
  public function saveAdd($values = null) {
    if(!$values) $values = $this->getValues();

    $cls = $this->entityName;
    $obj = new $cls;

    // Set values
    $this->_setEntityValues($obj, $values, 'add');

    $obj->persist();
    $obj->flush();
  }

  /**
   * Saves form which clones an existing entity
   * @param array $values Values to be saved
   * @return void
   */
  public function saveClone($values = null) {
    if(!$values) $values = $this->getValues();

    $cls = $this->entityName;
    $orig = \ActiveEntity\Entity::find($values['index'], $cls);
    if(method_exists($cls, 'clone')) {
      $obj = $orig->clone();
    }
    else {
      $obj = clone $orig;
    }

    // Set values
    $this->_setEntityValues($obj, $values, 'clone');

    $obj->persist();
    $obj->flush();
  }

  /**
   * Saves form which edits an existing entity
   * @param array $values Values to be saved
   * @return void
   */
  public function saveEdit($values = null) {
    if(!$values) $values = $this->getValues();

    $cls = $this->entityName;
    if(!$obj = \ActiveEntity\Entity::find($values['index'], $cls)) throw new NotFoundException;

    // Set values
    $this->_setEntityValues($obj, $values, 'edit');

    $obj->flush();
  }

  /**
   * Set values of an entity from form, it uses metadata to discover type
   * @param Entity $obj
   * @param array $values
   * @param string $action What action if performed: add, edit, clone
   * @return void
   */
  protected function _setEntityValues($obj, $values, $action) {
    // Update values
    foreach($this->metadata->getFieldDefinitions() as $def) {
      $fieldName = $def['fieldName'];
      if(!array_key_exists($fieldName, $values) || !$this->isFieldEditable($def)) continue;
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

    // Update mappings
    foreach($this->metadata->getAssociationMappings() as $def) {
      if(($def['type'] & \Doctrine\ORM\Mapping\ClassMetadataInfo::TO_ONE) === 0) continue; // It's mapping to many, dont process it by default
      $fieldName = $def['fieldName'];
      if(!array_key_exists($fieldName, $values) || !$this->isAssociationEditable($def, $action)) continue;
      $value = $values[$fieldName];

      // Find target entity and associate it
      $target = \ActiveEntity\Entity::find($value, $def['targetEntity']);
      $obj->$fieldName = $target;
    }
  }

  /**
   * Checks if field is editable
   * @param array $def Field definition
   * @return bool
   */
  protected function isFieldEditable($def) {
    if(!empty($def['id'])) return false; // It's index of table -> not editable

    return true; // Editable by default
  }

  /**
   * @param array $def
   * @param string $action
   * @return bool
   */
  protected function isAssociationEditable($def, $action) {
    if(isset($def['fieldMetadata']['ActiveEntity\\Annotations\\Immutable']) && $action != 'add') return false; // Immutable field and we are editing -> not possible
    if(isset($def['fieldMetadata']['ActiveEntity\\Annotations\\Required'])) return true;
    if(isset($def['fieldMetadata']['ActiveEntity\\Annotations\\Editable'])) return true;

    return false;
  }
  
  
  
}
