<?php

namespace app\models\dj;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "dj.user".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property string $password_reset_code
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property AccountSettings $accountSettings
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dj.user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'email', 'password'], 'required'],
            [['email_verified_at', 'created_at', 'updated_at'], 'safe'],
            [['name', 'email', 'password'], 'string', 'max' => 255],
            [['password_reset_code'], 'string', 'max' => 8],
            [['remember_token'], 'string', 'max' => 100],
            [['email'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'email_verified_at' => 'Email Verified At',
            'password' => 'Password',
            'remember_token' => 'Remember Token',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * {@inheritdoc}
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

	/**
	 * Gets the password, optionally stripping out the salt.
	 *
	 * @param bool $includeSalt
	 * @return string
	 */
    public function getPassword(bool $includeSalt = true): string
	{
		if($includeSalt)
		{
			return $this->password;
		}
		else
		{
			$passwordParts = explode('|', $this->password);
			return $passwordParts[0] ?? '';
		}
	}

	/**
	 * Gets the salt from the password.
	 *
	 * @return string
	 */
	public function getSalt()
	{
		$passwordParts = explode('|', $this->password);
		return $passwordParts[1] ?? '';
	}

    public function validateAuthKey($authKey)
	{
		$salt = $this->getSalt();

		return $this->hashPassword($authKey . $salt) === $this->getPassword(false);
	}

	public function generateSalt(): string
	{
		$alphasLower = range('a', 'z');
		$alphasUpper = range('A', 'Z');
		$specials = ['!', '@', '#', '$', '%', '^', '&', '*', '(', ')'];
		$numerals = range('0', '9');

		$allCharacters = array_merge($alphasLower, $alphasUpper, $specials, $numerals);
		$salt = '';
		for($i=0; $i<16; $i++)
		{
			$salt .= $allCharacters[random_int(0, count($allCharacters) - 1)];
		}
		return $salt;
	}

	/**
	 * Takes a user-entered password, adds a salt, hashes it,
	 * and returns it with salt appended for DB insertion.
	 *
	 * @param string $password
	 * @return string
	 */
	public function generatePassword(string $password)
	{
		$salt = $this->generateSalt();
		return $this->hashPassword($password . $salt) . '|' . $salt;
	}

	/**
	 * Hashes a password using SHA-512.
	 *
	 * @param string $authKey
	 * @return string
	 */
	public function hashPassword(string $authKey): string
	{
		return hash('sha512', $authKey);
	}

	public function getAuthKey()
	{
		return $this->password;
	}

	public function getId()
	{
		return $this->id;
	}

	public static function findIdentity($id)
	{
		return self::findOne($id);
	}

	public static function findIdentityByAccessToken($token, $type = null)
	{
		//What is this for?
		return NULL;
	}

	/**
	 * Gets the account settings for a user, creating new ones if none
	 * currently exist.
	 *
	 * @return AccountSettings
	 */
	public function loadAccountSettings(): AccountSettings
	{
		$accountSettings = $this->accountSettings;
		if(!$accountSettings)
		{
			$accountSettings = new AccountSettings();
			$this->link('accountSettings', $accountSettings);
			$this->save();
		}

		return $this->accountSettings;
	}

	/**
	 * @return AccountSettingsQuery
	 */
	public function getAccountSettings()
	{
		return $this->hasOne(AccountSettings::class, ['user_id' => 'id']);
	}
}
