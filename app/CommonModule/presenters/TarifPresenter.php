<?php

namespace CommonModule;

use Nette\Application\AppForm,
	Nette\Forms\Form,
  Nette\Forms,
  DoctrineForm,
  ActiveEntity\Entity;


class TarifPresenter extends BasePresenter
{
  // List of entity aliases (for listing, adding, editing, removing)
  protected $entityAliases = array(
    'tarif'    => 'Tarif',
    'flag'    => 'TarifFlag',
    'rychlost' => 'TarifRychlost',
  );

  /**
   * Render detail of tarif
   * @param int $id
   * @return void
   */
  function renderDetail($id) {
    $this->template->Tarif = \Tarif::find($id);
    $this->template->Speeds = \TarifFlag::getRepository()->findAll();
  }
  
}  