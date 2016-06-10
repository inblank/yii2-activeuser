<?php
use yii\helpers\Html;
/** @var \inblank\activeuser\models\User $user */
?>
Link to <?= Html::a('restore', ['/activeuser/account/password', 'token'=>$user->token])?>.
