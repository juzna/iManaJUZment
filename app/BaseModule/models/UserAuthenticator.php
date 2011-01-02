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
	public function authenticate(array $credentials)
	{
		switch($credentials[0]) {
		  case self::MODE_PASS: return $this->authenticateByPassword($credentials[1], $credentials[2]);
		  case self::MODE_OPENID: return $this->authenticateByOpenId($credentials[1]);
		  default: throw new AuthenticationException("Invalid authentication mode");
	  }
  }
  
  private function authenticateByPassword($username, $password) {
		$row = dibi::select('*')->from('users')->where('username=%s', $username)->fetch();

		if (!$row) {
			throw new AuthenticationException("User '$username' not found.", self::IDENTITY_NOT_FOUND);
		}

		if ($row->password !== $this->calculateHash($password)) {
			throw new AuthenticationException("Invalid password.", self::INVALID_CREDENTIAL);
		}

		unset($row->password);
		return new Nette\Security\Identity($row->id, NULL, $row);
	}
	
	/**
	 * Computes salted password hash.
	 * @param  string
	 * @return string
	 */
	public function calculateHash($password)
	{
		return md5($password . str_repeat('*random salt*', 10));
	}
  
  /**
  * Authenticate via OpenID URI
  */	
	private function authenticateByOpenId($uri) {
	  $row = dibi::select('*')->from('openid')->where('openid=%s', $uri)->fetch();
	  if(!$row) throw new AuthenticationException("Unknown OpenID");
	  
	  // Find user
	  $user = dibi::select('*')->from('users')->where('id=%i', $row->userid)->fetch();
	
		unset($user->password);
		return new Nette\Security\Identity($user->id, NULL, $user);
	}
}
