<?php

namespace app\models;

use app\models\dj\User;
use Yii;
use yii\base\Model;

/**
 * RegistrationForm is the model behind the registration form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class RegistrationForm extends Model
{
    public $email;
    public $name;
    public $password;
    public $password_verify;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // email and password are both required
            [['email', 'name', 'password', 'password_verify'], 'required'],
			[['email'], 'email'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            ['email', 'validateEmail'],
        ];
    }

	public function validateEmail($attribute, $params)
	{
		if (!$this->hasErrors())
		{
			if($this->getUser())
			{
				$this->addError($attribute, "A user with that email address already exists.");
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
    public function validatePassword($attribute, $params)
    {
		if (!$this->hasErrors())
		{
			if($this->password !== $this->password_verify)
			{
				$this->addError($attribute, "The password and its verification do not match.");
			}
		}
	}

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function register()
    {
        if ($this->validate()) {
			$user = new User();
			$user->email = $this->email;
			$user->name = $this->name;
			$user->password = $user->generatePassword($this->password);
			if ($user->save())
			{
				//Assign basic permission for access to dreams
				$dreamerRole = Yii::$app->getAuthManager()->getRole('dreamer');
				Yii::$app->getAuthManager()->assign($dreamerRole, $user->getId());

				//Send welcome email
				$user->sendEmail("Welcome to Celial!",
"
Dear {$user->name},

Thank you for registering on Celial!

We hope you have a great experience recording and analyzing your dreams.

Please let us know if you have any issues with the site or if you have any suggestions.

Sincerely,
The Celial Team
"
				);

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
