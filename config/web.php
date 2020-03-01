<?php

require_once __DIR__ . '/../utilities/Settings.php';

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
    	'authManager' => [
    		'class' => 'yii\rbac\DbManager'
		],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'bTRZcTuSFBTMaP_FqxztPLlfiBZBTR1s',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\dj\User',
            'enableAutoLogin' => true,
			'loginUrl' => ['user/login']
        ],
        'errorHandler' => [
            'errorAction' => 'user/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
			'transport' => [
				'class' => 'Swift_SmtpTransport',
				'host' => \app\utilities\Settings::get('smtp', 'host'),
				'port' => \app\utilities\Settings::get('smtp', 'port'),
				'username' => \app\utilities\Settings::get('smtp', 'username'),
				'password' => \app\utilities\Settings::get('smtp', 'password'),
				'encryption' => \app\utilities\Settings::get('smtp', 'encryption')
			]
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
				//Special URLs
				'dreamconcept/<id:[0-9]+>/words' => 'dreamconcept/conceptwords',

            	//Allow for UUIDs in otherwise normal URLs
            	'<controller:[a-zA-Z0-9]+>/<action:[a-zA-Z0-9]+>/<id:[a-f0-9\-]+>' => '<controller>/<action>',
            ],
        ],
		'assetManager' => [
			//Disable Yii's BS as we are using our own
			'bundles' => [
				'yii\web\JqueryAsset' => [
					'js'=>[]
				],
				'yii\bootstrap\BootstrapPluginAsset' => [
					'js'=>[]
				],
				'yii\bootstrap\BootstrapAsset' => [
					'css' => [],
				],
			]
		]
    ],
    'params' => $params,
	'defaultRoute' => 'dream/index'
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    /*$config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];*/

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
		'generators' => [ //here
			'crud' => [ // generator name
				'class' => 'yii\gii\generators\crud\Generator',
				'templates' => [
					'Dream Journal Default' => '@app/codeTemplates/crud/dj-default'
				]
			]
		],
    ];
}

return $config;
