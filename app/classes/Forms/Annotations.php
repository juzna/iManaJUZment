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


/**
 * Annotations specific for Forms
 * Can define how automatic forms are rendered in DoctrineForm class
 */

namespace Juz\Forms\Annotations;
use \Doctrine\Common\Annotations\Annotation;

/**
 * Given field has overridden default behaviour by another annotations
 */
class Override extends Annotation {}


/**
 * Given form field is meant to be a selectbox for another entity
 */
class EntitySelect extends Annotation {
  public $targetEntity;
  public $dependencies;
  public $fieldId;
  public $fieldName;
}
