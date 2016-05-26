<?php
return [
    'id' => 'unitTest',
    'basePath' => __DIR__ . '/../app',
    'components'=>[
        'db'=>[
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=testdb',
            'username' => 'travis',
            'password' => '',
            'charset' => 'utf8',
        ],
        'mailer'=>[
            'class'=>'app\components\MailMock',
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
