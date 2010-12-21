<?php

namespace CustomerModule;

use Nette\Application\AppForm,
	Nette\Forms\Form,
	Doctrine;



class DashboardPresenter extends \DashboardPresenter {
  // List of entity aliases (for listing, adding, editing, removing)
  protected $entityAliases = array(
    'customer'  => 'Customer',
    'ip'        => 'CustomerIP',
    'address'   => 'CustomerAddress',
    'contact'   => 'CustomerContact',
    'tariff'    => 'CustomerTariff',
    'inactivity'=> 'CustomerInactivity',
  );

  /**
   * List of customers
   */
  function renderDefault() {
    $this->template->customers = \Customer::getRepository()->findAll();
  }

  /**
   * Render detail of customer
   * @param int $id
   * @return void
   */
  function renderDetail($id) {
    $this->template->setParams(\Customer::find($id)->toArray());
  }


}
