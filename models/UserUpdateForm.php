<?php

namespace app\models;

use app\models\dj\User;
use Yii;
use yii\base\Model;

class UserUpdateForm extends Model
{
	public $email;
	public $name;
	public $new_password;
	public $new_password_verify;
	public $current_password;

	private $_user = false;

	public function getCurrentUser(): User
	{
		return Yii::$app->getUser()->getIdentity();
	}


	/**
	 * @return array the validation rules.
	 */
	public function rules()
	{
		return [
			// email and password are both required
			[['email', 'name', 'current_password'], 'required'],
			[['email'], 'email'],
			// password is validated by validatePassword()
			['new_password', 'validateNewPassword'],
			['new_password_verify', 'string'],
			['current_password', 'validateCurrentPassword'],
			['email', 'validateEmail'],
		];
	}

	public function validateEmail($attribute, $params)
	{
		if (!$this->hasErrors())
		{
			$user = $this->getUser();
			if($user && $user->getId() != $this->getCurrentUser()->getId())
			{
				$this->addError($attribute, "Another user with that email address already exists.");
			}
		}
	}

	public function validateCurrentPassword($attribute, $params)
	{
		if (!$this->hasErrors())
		{
			$user = $this->getCurrentUser();
			if(!$user->validateAuthKey($this->current_password))
			{
				$this->addError($attribute, "Incorrect current password entered.");
			}
		}
	}

	/**
	 * Validates the password.
	 * This method serves as the inline validation for password.
	 *
	 * @param string $attribute the attribute currently being validated
	 * @param array $params the additional name-value pairs given in the rule
	 */
	public function validateNewPassword($attribute, $params)
	{
		if (!$this->hasErrors())
		{
			if($this->new_password !== $this->new_password_verify)
			{
				$this->addError($attribute, "The password and its verification do not match.");
			}
		}
	}

	/**
	 * Logs in a user using the provided username and password.
	 * @return bool whether the user is logged in successfully
	 */
	public function update()
	{
		if ($this->validate()) {
			$user = $this->getCurrentUser();

			//Update the user's email and name
			$user->email = $this->email;
			$user->name = $this->name;

			//Update the password if a new one was given
			if(!empty($this->new_password))
			{
				$user->password = $user->generatePassword($this->new_password);
			}

			if($user->save())
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Finds user by [[username]]
	 *
	 * @return User|null
	 */
	public function getUser()
	{
		if ($this->_user === false) {
			$this->_user = User::find()->andWhere(['email' => $this->email])->one();
		}

		return $this->_user;
	}
}