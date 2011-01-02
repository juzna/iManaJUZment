<?php

namespace CommonModule;

use Nette\Application\AppForm,
	Nette\Forms\Form,
  Nette\Forms,
  DoctrineForm,
  ActiveEntity\Entity;


class TarifPresenter extends \DashboardPresenter {
  // List of entity aliases (for listing, adding, editing, removing)
  protected $entityAliases = array(
    'tarif'    => 'Tariff',
    'flag'    => 'TariffFlag',
    'rychlost' => 'TariffSpeed',
  );

  /**
   * Render detail of tarif
   * @param int $id
   * @return void
   */
  function renderDetail($id) {
    $this->template->Tarif = \Tariff::find($id);
    $this->template->Speeds = \TariffFlag::getRepository()->findAll();
  }
  
}  