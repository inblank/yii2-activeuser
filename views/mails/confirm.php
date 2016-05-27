<?php
/**
 * @var \inblank\activeuser\models\User $user
 */
use yii\helpers\Html;

echo Html::a('Confirm email', ['/activeuser/account/confirm', 'token'=>$user->token]);
