<?php

namespace PaymentModule;

class DashboardPresenter extends \DashboardPresenter {
  // List of entity aliases (for listing, adding, editing, removing)
  protected $entityAliases = array(
    'payment'  => 'Payment',

  );


  /**
   * Detail of a payment
   * @param int $id Payment ID
   */
  public function renderDetail($id) {
    $this->template->p = \Payment::find($id);
  }
}