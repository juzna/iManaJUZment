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
    switch($method) {
      case 'md5':
      case '':
        return md5($password);

      case 'sha1':
        return sha1($password);

      default:
        throw new \NotImplementedException("Algorithm $method");
    }
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
