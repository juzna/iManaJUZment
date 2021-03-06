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


namespace TestModule;

class TestPresenter extends \BasePresenter {

  /**
   * @return void
   */
  function renderTabpanel() {
    $this->template->tabpanel_page = @$_GET['tabpanel_page'];
  }


  function actionChange() {
    $ip = \APIP::find(1);
    $ip->description .= 'X';
    $ip->flush();

    exit;
  }

  function renderBehavioralMetadata() {
    \Nette\Debug::dump(\APSwIf::getClassMetadata()->getFieldMapping('rxmin'));

  }

  function createComponentTestForm() {
    $frm = new \Nette\Application\AppForm;
    $frm['ap'] = new \Juz\Form\EntitySelectPicker('Acess Point', 'AP');
    $frm['color'] = new \Juz\Form\ColorPicker('Select color');
    $frm['date'] = new \Juz\Form\DatePicker('Added');
    return $frm;
  }
}
