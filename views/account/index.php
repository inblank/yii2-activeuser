<?php
/**
 * @var \inblank\activeuser\models\User $user user data
 */

use yii\helpers\Html;

$this->title = $user->name;
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-xs-12 col-md-2">
        <div class="thumbnail">
            <?= Html::img($user->imageUrl, ['alt' => $user->name, 'title' => $user->name]); ?>
        </div>
    </div>
    <div class="col-xs-12 col-md-10">
        <h3><?= $user->name ?></h3>
        <?php if (!Yii::$app->user->isGuest): ?>
            <?= Html::a(
                Yii::t('activeuser_frontend', 'Update'),
                ['update'],
                ['class' => 'btn btn-primary']
            ) ?>
            <?= Html::a(
                Yii::t('activeuser_frontend', 'Change Password'),
                ['change-password'],
                ['class' => 'btn btn-warning']
            ) ?>
            <?= Html::a(
                Yii::t('activeuser_frontend', 'Sign out'),
                ['logout'],
                ['class' => 'btn btn-danger']
            ) ?>
        <?php endif; ?>
    </div>
</div>