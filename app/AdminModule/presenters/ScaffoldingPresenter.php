<?php

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
