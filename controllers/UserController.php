<?php

namespace app\controllers;

use app\components\gui\Breadcrumb;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use app\models\LoginForm;
use app\models\RegistrationForm;
use app\models\ContactForm;

class UserController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function beforeAction($action)
	{
		$this->addBreadcrumb(new Breadcrumb('Dream Journal'));
		return parent::beforeAction($action); // TODO: Change the autogenerated stub
	}

	/**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
		$this->addBreadcrumb(new Breadcrumb('Login', '', true));

        if (!Yii::$app->user->isGuest)
        {
            return $this->goHome();
        }

        $invalidLogin = false;
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login())
        {
			return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionRegister()
	{
		$this->addBreadcrumb(new Breadcrumb('Register', '', true));

		if (!Yii::$app->user->isGuest) {
			return $this->goHome();
		}

		$model = new RegistrationForm();
		if ($model->load(Yii::$app->request->post()) && $model->register()) {
			return $this->goBack();
		}

		$model->password = '';
		$model->password_verify = '';
		return $this->render('register', [
			'model' => $model,
		]);
	}

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }
}
