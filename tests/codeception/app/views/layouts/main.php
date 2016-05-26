<?php
/**
 * @var string $content
 */
if (Yii::$app->user->getIsGuest()) {
    echo \yii\helpers\Html::a('Login', ['/activeuser/account/login']);
    echo \yii\helpers\Html::a('Registration', ['/activeuser/account/register']);
} else {
    echo \yii\helpers\Html::a('Logout', ['/activeuser/account/logout']);
}

echo $content;

