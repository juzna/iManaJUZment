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


namespace AdminModule;

use ActiveEntity\Entity;

class ScaffoldingPresenter extends \BasePresenter {
  /**
   * Scaffolding for PHP code creating a form
   * @param string $entity Entity name
   */
  function renderFormCreator($entity) {
    $frm = new \DoctrineFormCreator($entity);
    $this->template->code = $frm->__toString();
    $this->template->setFile($this->getModulePath('templates') . '/Scaffolding.code.phtml');
  }

  /**
   * Scaffolding for a HTML code of a form
   * @param string $entity Entity name
   * @return void
   */
  function renderForm($entity) {
    $frm = new \DoctrineForm($entity);
    $frm->setRenderer(new \Nette\Forms\ScaffoldingRenderer());
    $code = $frm->__toString();

    $this->template->code = $code;
    $this->template->setFile($this->getModulePath('templates') . '/Scaffolding.code.phtml');
  }

  /**
   * Variables of an entity
   * @param  $entity
   * @return void
   */
  function renderEntityVariables($entity) {
    $prefix = @$_GET['prefix'];
    $prefix = $prefix ? ($prefix . '->') : '';

    $metadata = Entity::getClassMetadata($entity);
    foreach($metadata->getAllFieldNames() as $fieldName) {
      $label = ucfirst($fieldName);

      echo "<tr><th>$label:</th><td>{\$$prefix$fieldName}</td></tr>\n";
    }
    exit;
  }



}
