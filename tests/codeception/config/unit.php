<?php
return [
    'id' => 'unitTest',
    'basePath' => __DIR__ . '/../app',
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;dbname=testdb',
            'username' => 'travis',
            'password' => '',
            'charset' => 'utf8',
        ],
        'security' => [
            'passwordHashCost' => 4,
        ],
    ],
    'bootstrap' => [
        [
            'class' => 'inblank\activeuser\Bootstrap',
        ],
    ],
    'modules' => [
        'activeuser' => 'inblank\activeuser\Module',
    ],
];
