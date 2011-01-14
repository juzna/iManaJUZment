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

namespace BaseModule;

class LayoutPresenter extends BasePresenter {

  function renderDefault() {
    $this->template->list = \LayoutFactory::getSupportedLayouts();
    $this->template->selected = $this->getHttpRequest()->getCookie('layout');
  }

  function actionSet($layout) {
    $this->getHttpResponse()->setCookie('layout', $layout, strtotime('+1 month'), null, null, null, false);
    $this->flashMessage('Layout has been set');
    $this->redirect('default');
  }
}
