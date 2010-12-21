<?php

 
class DoctrineFormCreator extends DoctrineForm {
  private $code = array();

  /**
  * Init all fields from metadata
  */
  protected function _initFields() {
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
            $this->code[] = '$frm->addText(' . var_export($fieldName, true) . ', ' . var_export($label, true) . ');';
            break;

          case 'boolean':
            $this->code[] = '$frm->addCheckBox(' . var_export($fieldName, true) . ', ' . var_export($label, true) . ');';
            break;
        }
      }

      // It's mapping
      elseif($this->metadata->hasAssociation($fieldName)) {
        $def = $this->metadata->getAssociationMapping($fieldName);
        if(isset($def['fieldMetadata']['ActiveEntity\\Annotations\\Required'])) {
        }
      }
    }
  }

  public function render() {
    echo implode("\n", $this->code) . "\n";
  }
}
