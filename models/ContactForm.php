<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ContactForm extends Model
{
    public $name;
    public $email;
    public $subject;
    public $body;
    public $verifyCode;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['name', 'email', 'subject', 'body'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
            // verifyCode needs to be entered correctly
            ['verifyCode', 'captcha', 'captchaAction' => 'user/captcha'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Verification Code',
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @return bool whether the model passes validation
     */
    public function contact()
    {
        if ($this->validate()) {
        	$body = $this->body;

        	if($user = Yii::$app->getUser()->getIdentity())
			{
				$body .= "\r\n\r\nUser ID: " . $user->getId();
			}

            Yii::$app->mailer->compose()
                ->setTo(Yii::$app->params['webmasterEmail'])
                ->setFrom([$this->email => $this->name])
                ->setReplyTo([$this->email => $this->name])
                ->setSubject($this->subject)
                ->setTextBody($body)
                ->send();

            return true;
        }
        return false;
    }
}
