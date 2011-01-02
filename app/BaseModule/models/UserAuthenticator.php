<?php

use Nette\Object,
	Nette\Security\AuthenticationException,
	Nette\Environment;


/**
 * Users authenticator.
 */
class UserAuthenticator extends Object implements Nette\Security\IAuthenticator {
  const MODE_PASS = 1;
  const MODE_OPENID = 2;

	/**
	 * Performs an authentication
	 * @param  array
	 * @return IIdentity
	 * @throws AuthenticationException
	 */
	public function authenticate(array $credentials) {
		switch($credentials[0]) {
		  case self::MODE_PASS: return $this->authenticateByPassword($credentials[1], $credentials[2]);
		  case self::MODE_OPENID: return $this->authenticateByOpenId($credentials[1]);
		  default: throw new AuthenticationException("Invalid authentication mode");
	  }
  }
  
  private function authenticateByPassword($username, $password) {
    try {
      /** @var $user User */
      $user = User::getRepository()->findOneByUsername($username);
    }
    catch(Exception $e) {
      throw new AuthenticationException("User '$username' not found.", self::IDENTITY_NOT_FOUND);
    }

    if($this->calculateHash($password, $user->hashMethod) != $user->password) {
      throw new AuthenticationException("Invalid password.", self::INVALID_CREDENTIAL);
    }

    return $this->authenticateUser($user);
	}
	
	/**
	 * Computes salted password hash.
	 * @param  string
	 * @return string
	 */
	public function calculateHash($password, $method) {
		return md5($password . str_repeat('*random salt*', 10));
	}
  
  /**
  * Authenticate via OpenID URI
  */	
	private function authenticateByOpenId($uri) {
    $openId = UserOpenId::getRepository()->findOneByIdentity($uri);
    return $this->authenticateUser($openId->user);
	}

  private function authenticateUser(\User $user) {
    if(!$user->active) throw new AuthenticationException("User is not active, ask admin to allow access");

    return new Nette\Security\Identity($user->ID, NULL, $user->toArray(false));
  }
}
