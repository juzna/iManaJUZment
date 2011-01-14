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


namespace PaymentModule;
use \Nette\Application\AppForm;

class IncomingPresenter extends \BasePresenter {
  /**
   * Show form for choosing a customer
   */
  function renderChooseCustomer() {
    // nothing...
  }

  function handlePaymentForm() {
    $c = \Customer::getRepository()->findOneBy(array('contractNumber' => @$_POST['contractNo']));
    if($c) $this->redirect('paymentForm', array('custId' => $c->custId));
    else throw new \InvalidArgumentException("Customer not found");
  }

  /**
   * Show form for adding new payment
   * @param int $custId
   */
  function renderPaymentForm($custId) {
    /** @var $customer Customer */
    $this->template->customer = $customer = \Customer::find($custId);
    $this->template->paymees = $customer->getAvailablePaymees();
    $this->template->form = $frm = $this->getWidget('paymentForm');

    $frm['customer']->setValue($custId);
  }

  function createComponentPaymentForm() {
    $frm = new AppForm;
    $frm->addHidden('customer');
    $frm->addText('method', 'Method');
    $frm->addText('dateAdded', 'Date added');
    $frm->addText('datePaid', 'Date paid');
    $frm->addText('amount', 'Amount');
    $frm->addText('currency', 'Currency');
    $frm->addSubmit('save', 'Add new payment');

    $frm->onSubmit[] = callback($this, 'savePayment');

    return $frm;
  }

  function savePayment(AppForm $frm) {
    $data = $frm->getHttpData();
    \Nette\Debug::dump($data);
    $frm2 = new \DoctrineForm('Payment'); // Save using doctrine form

    /** @var $payment Payment */
    $payment = $frm2->saveAdd($data, false);

    foreach($data['paymee'] as $item) {
      if(empty($item['use'])) continue;
      $amount = $item['amount'];

      switch($item['type']) {
        case 'tariff':
          $payment->addPaymee(new \PaymeeTariff($payment, $amount, \CustomerTariff::find($item['index']), $item['months']));
          break;

        case 'install-fee':
          $payment->addPaymee(new \PaymeeInstalationFee($payment, $amount, \CustomerInstalationFee::find($item['index'])));
          break;

        case 'service-fee':
          $payment->addPaymee(new \PaymeeServiceFee($payment, $amount, \CustomerServiceFee::find($item['index'])));
          break;
      }
    }

    // Save it all
    $payment->persist();
    em()->flush();

    $this->redirect('dashboard:detail', array($payment->ID));
  }
  
}