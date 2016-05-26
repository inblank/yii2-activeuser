<?php
use inblank\activeuser\models\User;

$date = date('Y-m-d');
$sec = Yii::$app->security;

return [
    'active'=>[
        'id'=>1,
        'status'=>User::STATUS_ACTIVE,
        'email' => 'active@example.com',
        'pass_hash' => $sec->generatePasswordHash('active'),
        'name'=>'Active',
        'auth_key'=>$sec->generateRandomString(40),
        'access_token'=>$sec->generateRandomString(40),
        'registered_at'=>$date,
    ],
    'blocked'=>[
        'id'=>2,
        'status'=>User::STATUS_BLOCKED,
        'email' => 'blocked@example.com',
        'pass_hash' => $sec->generatePasswordHash('blocked'),
        'name'=>'Blocked',
        'access_token'=>$sec->generateRandomString(40),
        'registered_at'=>$date,
    ],
    'unconfirmed'=>[
        'id'=>3,
        'status'=>User::STATUS_CONFIRM,
        'email' => 'unconfirmed@example.com',
        'pass_hash' => $sec->generatePasswordHash('unconfirmed'),
        'name'=>'Unconfirmed',
        'access_token'=>$sec->generateRandomString(40),
        'token'=>$sec->generateRandomString(40),
        'token_created_at'=>time()-60*30, // 30 minutes ago
        'registered_at'=>$date,
    ],
    'emptyauth'=>[
        'id'=>4,
        'status'=>User::STATUS_ACTIVE,
        'email' => 'emptyauth@example.com',
        'pass_hash' => $sec->generatePasswordHash('emptyauth'),
        'name'=>'Empty auth key',
        'access_token'=>$sec->generateRandomString(40),
        'registered_at'=>$date,
    ],
    'emptyaccesstoken'=>[
        'id'=>5,
        'status'=>User::STATUS_ACTIVE,
        'email' => 'emptyaccesstoken@example.com',
        'pass_hash' => $sec->generatePasswordHash('emptyaccesstoken'),
        'name'=>'Empty access token',
        'registered_at'=>$date,
    ],
    'restore'=>[
        'id'=>6,
        'status'=>User::STATUS_RESTORE,
        'email' => 'restore@example.com',
        'pass_hash' => $sec->generatePasswordHash('restore'),
        'name'=>'Resotre',
        'token'=>$sec->generateRandomString(40),
        'token_created_at'=>time()-60*30, // 30 minutes ago
        'registered_at'=>$date,
    ],
];
