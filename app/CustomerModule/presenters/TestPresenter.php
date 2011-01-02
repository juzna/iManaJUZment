<?php

namespace CustomerModule;

class TestPresenter extends BasePresenter {

  public function renderTariff() {
    /** @var $customer Customer */
    $customer = \Customer::find(10);
    //\Nette\Debug::dump(\PaymeeTariff::getRepository()->findAll());
    //\Nette\Debug::dump($customer->Tariffs[0]->getPaymees());
    print_r($customer->Tariffs[0]->calculatePrepaidDate());
    exit;
  }

}