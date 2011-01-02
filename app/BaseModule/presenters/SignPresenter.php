<?php

namespace BaseModule;

use Nette\Application\AppForm,
	Nette\Forms\Form,
	Nette\Security\AuthenticationException;



class SignPresenter extends BasePresenter
{
	/** @persistent */
	public $backlink = '';



	public function startup()
	{
		parent::startup();
		$this->session->start(); // required by $form->addProtection()
	}



	/********************* component factories *********************/



	/**
	 * Sign in form component factory.
	 * @return Nette\Application\AppForm
	 */
	protected function createComponentSignInForm()
	{
		$form = new AppForm;
		$form->addText('username', 'Username:')
			->addRule(Form::FILLED, 'Please provide a username.');

		$form->addPassword('password', 'Password:')
			->addRule(Form::FILLED, 'Please provide a password.');

		$form->addSubmit('send', 'Sign in');

		$form->onSubmit[] = callback($this, 'signInFormSubmitted');
		return $form;
	}



	public function signInFormSubmitted($form)
	{
		try {
			$this->user->login(\UserAuthenticator::MODE_PASS, $form['username']->value, $form['password']->value);
			$this->application->restoreRequest($this->backlink);
			$this->redirect('Dashboard:');

		} catch (AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}
	
	public function actionSignViaGoogle() {
	  $mode = @$_GET['openid_mode'];
    
    // Initialize OpenID
    $openid = new \LightOpenID;
    $openid->realm     = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
    $openid->returnUrl = $openid->realm . $this->link('signViaGoogle');
    
    // First request
	  if(empty($mode)) {
      $openid->identity = 'http://juzna.cz/openid/google'; //https://www.google.com/accounts/o8/id';
      header('Location: ' . $openid->authUrl());
      exit;
    }
    
    elseif($mode == 'cancel') {
      echo "Auth canceled\n";
      exit;
    }
    
    if($openid->validate()) {
		  $this->user->login(\UserAuthenticator::MODE_OPENID, $openid->identity);
		  $this->application->restoreRequest($this->backlink);
		  $this->redirect('Dashboard:');
	  }
	  else die("OpenID failed");
	}

	public function actionOut()
	{
		$this->getUser()->logout();
		$this->flashMessage('You have been signed out.');
		$this->redirect('in');
	}

}
