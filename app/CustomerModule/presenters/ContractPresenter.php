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


namespace CustomerModule;


use Nette\Application\AppForm,
	Nette\Forms\Form,
	Doctrine,
  ActiveEntity\Entity,
  dibi;


/**
 * Manipulating with customer contracts
 */
class ContractPresenter extends BasePresenter {

  function handleCreate() {
    $frm = new \DoctrineFormCreator('Customer'); $frm->render(); echo "\n\n";
    $frm = new \DoctrineFormCreator('CustomerAddress'); $frm->render(); echo "\n\n";
    $frm = new \DoctrineFormCreator('CustomerIP'); $frm->render(); echo "\n\n";
    $frm = new \DoctrineFormCreator('CustomerTariff'); $frm->render(); echo "\n\n";

    exit;
  }

  function renderScaffolding() {
    /** @var $frm \Nette\Forms\AppForm */
    $frm = $this->getComponent('contractForm');
    $frm->setRenderer(new \Nette\Forms\ScaffoldingRenderer());
    $code = $frm->__toString();


    if(@$_GET['save']) {
      $path = $this->getModulePath('templates') . '/contract.phtml';
      file_put_contents($path, $code);
    }
    else echo $code;

    exit;
  }

  function renderTpl() {
    $renderer = new \Nette\Forms\TemplateRenderer($this->formatTemplateFiles($this->getName(), 'form'));
    $this->templatePrepareFilters($renderer->getTemplate());

    $frm = $this->getComponent('contractForm');
    $frm->setRenderer($renderer);

    $frm->render();
    exit;

  }


  public function createComponentContractForm() {
    $frm = new AppForm;
    $frm->addProtection();

    // Customer info
    $frm->addGroup('Basic info')->setOption('prefix', 'cust');
    $frm->addText('contractNumber', 'Contract number');
    $frm->addText('password', 'Password');
    $frm->addText('activeSince', 'Active since');
    $frm->addCheckbox('accepted', 'Accepted?');
    $frm->addText('instalacniPoplatek', 'Instalacni poplatek');
    $frm->addText('doporucitel', 'Doporucitel');
    $frm->addText('sepsaniSmlouvy', 'Sepsani smlouvy')->setType('date');

    // Adresa info
    $frm->addGroup('Address')->setOption('prefix', 'address');
    $frm->addCheckBox('isOdberna', 'Je odberna?');
    $frm->addCheckBox('isFakturacni', 'je fakturacni?');
    $frm->addCheckBox('isKorespondencni', 'Je korespondencni?');
    $frm->addText('popis', 'Popis');
    $frm->addText('firma', 'Firma');
    $frm->addText('firma2', 'Firma2');
    $frm->addText('titulPred', 'Titul pred');
    $frm->addText('jmeno', 'Jmeno');
    $frm->addText('druheJmeno', 'Druhe jmeno');
    $frm->addText('prijmeni', 'Prijmeni');
    $frm->addText('druhePrijmeni', 'Druhe prijmeni');
    $frm->addText('titulZa', 'Titul za');
    $frm->addText('ICO', 'ICO');
    $frm->addText('DIC', 'DIC');
    $frm->addText('poznamka', 'Poznamka');
    $frm->addText('rodneCislo', 'Rodne cislo');
    $frm->addText('datumNarozeni', 'Datum narozeni');

    // IP address
    $frm->addGroup('IP address')->setOption('prefix', 'ip');;
    $frm->addText('IP', 'IP');
    $frm->addText('netmask', 'Netmask');
    $frm->addText('MAC', 'MAC');
    $frm->addSelectBox('l2parent', 'L2 parent', 'AP')->skipFirst('Select AP which is L2 parent');
    $frm->addText('l2parentIf', 'L2 parent if');
    $frm->addSelectBox('l3parent', 'L3 parent', 'AP')->skipFirst('Select AP which is L3 parent');
    $frm->addText('l3parentIf', 'L3 parent if');

    // Tariff
    $frm->addGroup('Tariff')->setOption('prefix', 'tariff');
    $frm->addSelect('tariff', 'Tariff', Entity::fetchPairs('ID', 'nazev', 'Tariff'))->skipFirst('Please select tariff');
    $frm->addText('comment', 'Comment');
    $frm->addCheckBox('specialniCeny', 'Specialni ceny?');
    $frm->addText('mesicniPausal', 'Mesicni pausal');
    $frm->addText('ctvrtletniPausal', 'Ctvrtletni pausal');
    $frm->addText('pololetniPausal', 'Pololetni pausal');
    $frm->addText('rocniPausal', 'Rocni pausal');
    $frm->addText('datumOd', 'Datum od');
    $frm->addText('datumDo', 'Datum do');
    
    $frm->addGroup();
    $frm->addSubmit('add', 'Add new contract');
    $frm->onSubmit[] = callback($this, 'onContractFormSubmitted');

    return $frm;
  }

  public function onContractFormSubmitted(AppForm $frm) {
    if(!$frm['add']->isSubmittedBy()) return;

    // Add new customer
    $cust = \DoctrineForm::createFromArray('Customer', $frm->getValues('cust'));
    $cust->Addresses->add($address = \DoctrineForm::createFromArray('CustomerAddress', $frm->getValues('address')));
    $cust->IPs->add(\DoctrineForm::createFromArray('CustomerIP', $frm->getValues('ip')));
    $cust->Tariffs->add(\DoctrineForm::createFromArray('CustomerTariff', $frm->getValues('tariff')));
    $cust->address = $address;

    $cust->persist();
    $cust->flush();

    $this->flashMessage('New customer added to database');
    $this->redirect('default');
  }


}