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
    echo $this->__toString();
  }

  public function __toString() {
    return implode("\n", $this->code) . "\n";
  }
}
