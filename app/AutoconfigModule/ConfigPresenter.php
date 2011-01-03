<?php

namespace AutoconfigModule;

class ConfigPresenter extends \Presenter {
	function actionTest() {
		require_once $this->dir . '/drivers/straightcore.php';
		
		$s = new \Autoconfig\Drivers\StraightCore('localhost', '17002');
		echo '<pre>';
		var_dump($s->getFirmwareVersion());
		print_r($s->getConfig());
		exit;
		die($s->request('status.asp'));
		
		var_dump($s);
	
	}


}
