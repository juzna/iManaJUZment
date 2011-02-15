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

use Nette\Forms\IFormControl,
  Nette\Forms\TextInput;


class ColorPicker extends TextInput {
  public function __construct($label = NULL, $cols = NULL, $maxLength = NULL) {
    parent::__construct($label, $cols, $maxLength);
    $this->setAttribute('class', 'colorpicker');
  }

  public function setValue($value) {
    // Check if this valid color value
    if(!static::isValid($value)) throw new \InvalidArgumentException('Passed value is not valid color');
    return parent::setValue($value);
  }

  public static function validateFilled(IFormControl $control) {
    return static::isValid($control->getValue());
  }

  /**
   * Check is this is a valiad value
   * @param string $value
   * @return bool
   */
  public static function isValid($value, $allowEmpty = true) {
    if($allowEmpty && trim($value) === '') return true;
    return preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/', $value);
  }
}
