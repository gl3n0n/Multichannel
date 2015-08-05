<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	private $_id;
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the Username and Password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		$user = Users::model()->findByAttributes(array('Username'=>$this->username));
		if(!$user)
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		elseif($user->Password!==md5($this->password))
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
		else
		{
			$this->errorCode=self::ERROR_NONE;
			$this->_id = $user->UserId;
			$this->setState('Username', $user->Username);
			$this->setState('ClientId', $user->ClientId);
			$this->setState('AccessType', $user->AccessType);
		}
		return !$this->errorCode;
	}

	public function getId()
	{
		return $this->_id;
	}
}