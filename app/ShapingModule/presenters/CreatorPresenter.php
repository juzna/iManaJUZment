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


namespace ShapingModule;


/**
 * Presenter for preparing and creating queues
 */
class CreatorPresenter extends \BasePresenter {
  protected $_creator;

  /**
   * Instantiate queue creator class
   */
  protected function getCreator() {
    if(!isset($this->_creator)) $this->_creator = new QueueCreator;
    return $this->_creator;
  }

	/**
	* Dump queue preview
	*/
	function actionPreview($i) {
		// Prepare queue, but not connect to driver
		$this->creator->prepare($i, null, null)->dump();
		exit;
	}
	
	/**
	* Show sync commands
	*/
	function actionSyncShow($i) {
		$driver = $this->creator->prepare($i)->getDriver();
		echo '<pre>';
		echo $driver->previewCommands(true);
		exit;
	}
	
	/**
	* Sync it
	*/
	function actionSyncDoit($i) {
		$this->creator->prepare($i)->getDriver()->commit();
		return 'DONE';
	}
}
