<?php

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
