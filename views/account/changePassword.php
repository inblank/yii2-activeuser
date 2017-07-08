<?php
/**
 * @var \inblank\activeuser\models\User $user user
 * @var \inblank\activeuser\models\forms\ChangePasswordForm $model change password form
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('activeuser_frontend', 'Change Password');
$this->params['breadcrumbs'][] = ['label' => $user->name, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-change-password">

    <h1><?= Yii::t('activeuser_frontend', 'Change Password') ?></h1>
    <div class="row">
        <div class="col-xs-12 col-md-2">
            <div class="thumbnail">
                <?= Html::img($user->imageUrl, ['alt' => $user->name, 'title' => $user->name]); ?>
            </div>
        </div>
        <div class="col-xs-12 col-md-10">
            <?php $form = ActiveForm::begin() ?>
            <?= $form->field($model, 'oldPassword', [
                'inputOptions' => [
                    'autofocus' => 'autofocus',
                    'autocomplete' => 'new-password',
                    'class' => 'form-control',
                    'tabindex' => '1',
                ],
            ])->passwordInput() ?>
            <?= $form->field($model, 'newPassword', [
                'inputOptions' => [
                    'class' => 'form-control',
                    'tabindex' => '2'
                ]
            ])->passwordInput() ?>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('activeuser_frontend', 'Change'), [
                    'class' => 'btn btn-primary', 'tabindex' => '3'
                ]) ?>
                <?= Html::a(Yii::t('activeuser_frontend', 'Cancel'),
                    ['view'],
                    ['class' => 'btn btn-warning', 'tabindex' => '4']
                )
                ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
