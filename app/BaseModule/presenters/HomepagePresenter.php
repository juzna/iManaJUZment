<?php

/**
 * My Application
 *
 * @copyright  Copyright (c) 2009 John Doe
 * @package    MyApplication
 */

namespace BaseModule;



/**
 * Homepage presenter.
 *
 * @author     John Doe
 * @package    MyApplication
 */
class HomepagePresenter extends BasePresenter
{

	public function renderDefault()
	{
		$this->template->message = 'We hope you enjoy this framework!';
	}

}
