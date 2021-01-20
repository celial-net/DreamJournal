<?php

require_once __DIR__ . '/../utilities/Settings.php';

return [
    'class' => 'yii\db\Connection',
    'dsn' => \app\utilities\Settings::get('db', 'dsn'),
    'username' => \app\utilities\Settings::get('db', 'user'),
    'password' => \app\utilities\Settings::get('db', 'password'),
    'charset' => 'utf8',
	'attributes' => [\PDO::ATTR_CASE => \PDO::CASE_LOWER]

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
