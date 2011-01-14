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

use Nette\Application\AppForm,
	Nette\Forms\Form,
	Nette\Security\AuthenticationException;



class SignPresenter extends BasePresenter {
	/** @persistent */
	public $backlink = '';

  /** @persistent Gained open id */
  public $openId;

  protected $public = true;


	public function startup() {
		parent::startup();
		$this->session->start(); // required by $form->addProtection()
	}

  /**
   * Check if we're in supported browser
   */
  protected function browserCheck() {
    // Browser capabilities check
    if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') &&
            !strpos($_SERVER['HTTP_USER_AGENT'], 'chromeframe')) {

      // Allow using IE
      if(@$_GET['useIE']) $_SESSION['useIE'] = true;
      if(empty($_SESSION['useIE'])) $this->redirect('browser');
    }
  }

  public function renderIn() {
    $this->browserCheck();
  }

  public function renderWelcome() {
    $this->browserCheck();
  }

	/********************* component factories *********************/


	/**
	 * Sign in form component factory.
	 * @return Nette\Application\AppForm
	 */
	protected function createComponentSignInForm() {
		$form = new AppForm;
		$form->addText('username', 'Username:')
			->addRule(Form::FILLED, 'Please provide a username.');

		$form->addPassword('password', 'Password:')
			->addRule(Form::FILLED, 'Please provide a password.');

		$form->addSubmit('send', 'Sign in');

		$form->onSubmit[] = callback($this, 'signInFormSubmitted');
		return $form;
	}

	public function signInFormSubmitted($form) {
		try {
			$this->user->login(\UserAuthenticator::MODE_PASS, $form['username']->value, $form['password']->value);
			$this->application->restoreRequest($this->backlink);
			$this->redirect('Dashboard:');

		} catch (AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}

  /** Open ID basic object */
  protected function getOpenIdAuthenticator($returnUrl) {
    // Initialize OpenID
    $openid = new \LightOpenID;
    $openid->realm     = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
    $openid->returnUrl = $openid->realm . $this->link($returnUrl);

    return $openid;
  }

  /** Signing in via OpenId */
	public function actionSignViaGoogle() {
	  $mode = @$_GET['openid_mode'];
    $openid = $this->getOpenIdAuthenticator('signViaGoogle');

    // First request
	  if(empty($mode)) {
      $openid->identity = 'https://www.google.com/accounts/o8/id';
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

  /** Registration form using OpenId */
  public function actionRegisterViaGoogle() {
    $mode = @$_GET['openid_mode'];
    $openid = $this->getOpenIdAuthenticator('registerViaGoogle');

    // First request
    if(empty($mode)) {
      $openid->identity = 'https://www.google.com/accounts/o8/id';
      header('Location: ' . $openid->authUrl());
      exit;
    }

    elseif($mode == 'cancel') {
      echo "Auth canceled\n";
      exit;
    }

    if($openid->validate()) {
      $this->openId = $openid->identity;
      $this->redirect('registerViaGoogle2');
    }
    else die("OpenID failed");
  }

  /** Registration form for OpenId (when identity is already gained) */
  function renderRegisterViaGoogle2() {
    if(empty($this->openId)) die("Open ID is not set");

    if(\UserOpenId::getRepository()->findOneByIdentity($this->openId)) throw new \Nette\Application\BadRequestException("User with gained openId already exists");

    $frm = $this->getWidget('registerViaGoogleForm');
    $frm['openId']->setValue($this->openId);
  }

  function createComponentRegisterViaGoogleForm() {
    $frm = new AppForm;
    $frm->addText('openId', 'Open ID');
    $frm->addText('username', 'User name');
    $frm->addText('realName', 'Full name');

    $frm->addSubmit('save', 'Register');
    $frm->onSubmit[] = callback($this, 'registerViaGoogleSubmitted');
    return $frm;
  }

  function registerViaGoogleSubmitted(AppForm $frm) {
    $user = new \User;
    $user->username = $frm['username']->getValue();
    $user->realName = $frm['realName']->getValue();
    new \UserOpenId($user, $frm['openId']->getValue());

    $user->persist();
    $user->flush();

    $this->flashMessage("New user has been added");
    $this->redirect('in');
  }



  /** Signing out */
	public function actionOut() {
		$this->getUser()->logout();
		$this->flashMessage('You have been signed out.');
		$this->redirect(':Base:Sign:welcome');
	}
}
