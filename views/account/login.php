<?php
/**
 * @var \inblank\activeuser\models\forms\LoginForm $model
 * @var \inblank\activeuser\Module $module
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('activeuser_general', 'Sign in');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loginpanel">
    <div class="loginpanel__header">
        <h3 class="loginpanel__title"><?= Html::encode($this->title) ?></h3>
    </div>
    <div class="loginpanel__body">
        <?php $form = ActiveForm::begin([
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'validateOnBlur' => false,
            'validateOnType' => false,
            'validateOnChange' => false,
        ]) ?>
        <?= $form->field($model, 'email', [
            'inputOptions' => [
                'autofocus' => 'autofocus',
                'class' => 'form-control',
                'tabindex' => '1',
            ],
        ]) ?>
        <?= $form->field($model, 'password', [
            'inputOptions' => [
                'class' => 'form-control',
                'tabindex' => '2'
            ]
        ])->passwordInput() ?>

        <?= $form->field($model, 'remember')->checkbox(['tabindex' => '4']) ?>

        <?= Html::submitButton(Yii::t('activeuser_general', 'Sign in'), [
            'class' => 'button', 'tabindex' => '3'
        ]) ?>

        <?php if ($module->enablePasswordRecovery): ?>
            <div class="loginpanel__recovery">
                <?= Html::a(Yii::t('activeuser_general', 'Forgot password?'), ['/activeuser/account/recovery'], ['tabindex' => '5']) ?>
            </div>
        <?php endif; ?>

        <?php ActiveForm::end(); ?>
    </div>
    <?php if ($module->enableConfirmation): ?>
        <div class="loginpanel__resend">
            <?= Html::a(Yii::t('activeuser_general', "Didn't receive confirmation message?"), ['/activeuser/account/resend']) ?>
        </div>
    <?php endif ?>
    <?php if ($module->enableRegistration): ?>
        <div class="loginpanel__register">
            <?= Html::a(Yii::t('activeuser_general', "Don't have an account? Sign up!"), ['/activeuser/account/register']) ?>
        </div>
    <?php endif ?>
</div>
