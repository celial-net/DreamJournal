<?php

// comment out the following two lines when deployed to production

require_once __DIR__ . '/../utilities/Settings.php';

$env = \app\utilities\Settings::get('app', 'env') ?: 'dev';
$debug = boolval(\app\utilities\Settings::get('app', 'debug'));

defined('YII_DEBUG') or define('YII_DEBUG', $debug);
defined('YII_ENV') or define('YII_ENV', $env);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();
