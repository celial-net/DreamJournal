<?php

namespace app\models;

use app\models\dj\User;
use yii\base\Model;

class PasswordResetRequestForm extends Model
{
	public $email;


	/**
	 * @return array the validation rules.
	 */
	public function rules()
	{
		return [
			// username and password are both required
			[['email'], 'required'],
			// rememberMe must be a boolean value
			['email', 'email']
		];
	}

	protected function getUser(): ?User
	{
		return User::find()->andWhere(['email' => $this->email])->one();
	}

	/**
	 * Always pretend to succeed so the user doesn't know if they
	 * reset someone's password or not.
	 *
	 * @return bool
	 */
	public function sendResetLink(): bool
	{
		$user = $this->getUser();
		if($user)
		{
			$code = '';
			$digits = range(0, 9);
			for($i=0; $i<8; $i++)
			{
				$code .= array_rand($digits);
			}

			$user->password_reset_code = $code;
			$user->save();

			//Email to user
			$user->sendEmail("Password Reset Request",
"
Dear {$user->name},

We have received your request to reset your password.

You can use the following link to reset your password:
http://my.celial.net/user/reset?code={$code}

If you did not initiate this request, you can disregard this email.

Sincerely,
The Celial Team
"
			);
		}
		return true;
	}
}