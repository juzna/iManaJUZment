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
    'servicefee' => 'CustomerServiceFee',
    'instalationfee' => 'CustomerInstalationFee',
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
    $this->template->c = \Customer::find($id);
  }

  /**
   * Items to be paid by customer
   * @param int $id
   */
  function renderRequestedPaymees($id) {
    $this->template->list = \Customer::find($id)->getAvailablePaymees();
  }


}
