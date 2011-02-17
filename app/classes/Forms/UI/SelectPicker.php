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
  Nette\Forms\FormControl;

class SelectPicker extends FormControl {
  protected $dataSource;

  /**
   * @param  string  label
   */
  public function __construct($label = NULL, $dataSource = null) {
    parent::__construct($label);
    $this->dataSource = $dataSource;
    $this->control->setName('select');
  }

  public function setDataSource($dataSource) {
    $this->dataSource = $dataSource;
  }

  public function getDataSource() {
    return $this->dataSource;
  }

  public function getControl() {
    $control = parent::getControl();
    $option = \Nette\Web\Html::el('option');
    $control->add((string) $option);

    foreach($this->getDataSource() as $key => $item) {
      $control->add((string) $option->value($key)->setText($item));
    }

    return $control;
  }
}
