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

class DatePicker extends \Neuron\Form\DatePicker {
  public function __construct($label = NULL, $cols = NULL, $maxLength = NULL) {
    parent::__construct($label, $cols, $maxLength);
    $this->setType('date');
  }

  public function getControl() {
    // TODO: add calendar icon to this
    // return (string) parent::getControl() . '<span>Date</span>';
    return parent::getControl();
  }
}
