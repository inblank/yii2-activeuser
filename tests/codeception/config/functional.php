<?php
$_SERVER['SCRIPT_FILENAME'] = YII_TEST_ENTRY_FILE;
$_SERVER['SCRIPT_NAME']     = YII_TEST_ENTRY_URL;
$_SERVER['SERVER_NAME']     = 'localhost';

return [
    'id' => 'functionalTest',
    'basePath' => __DIR__ . '/../app',
    'aliases' => [
        '@vendor'        => __DIR__.'/../../../vendor',
        '@bower'         => __DIR__.'/../../../vendor/bower-asset',
    ],
    'components'=>[
        'db'=>[
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=testdb',
            'username' => 'travis',
            'password' => '',
            'charset' => 'utf8',
        ],
        'request' => [
            'enableCsrfValidation'   => false,
            'enableCookieValidation' => false,
        ],
        'assetManager' => [
            'bundles' => null,
        ]
    ],
    'bootstrap'=>[
        [
            'class'=>'inblank\activeuser\Bootstrap',
        ],
    ],
    'modules'=>[
        'activeuser'=>'inblank\activeuser\Module',
    ],
];
