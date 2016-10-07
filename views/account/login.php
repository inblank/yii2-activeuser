<?php
/**
 * @var \inblank\activeuser\models\forms\LoginForm $model
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('activeuser_frontend', 'Sign in');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
        <div id="form-login" class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <?php $form = ActiveForm::begin([
                'enableAjaxValidation' => false,
                'enableClientValidation' => false,
                'validateOnBlur' => false,
                'validateOnType' => false,
                'validateOnChange' => false,
            ]) ?>
            <div class="panel-body">
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
                <?= Html::submitButton(Yii::t('activeuser_frontend', 'Sign in'), [
                    'class' => 'btn btn-primary btn-block', 'tabindex' => '3'
                ]) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="text-center">
            <?php if ($model->module->enablePasswordRestore): ?>
                <p class="login-restore">
                    <?= Html::a(Yii::t('activeuser_frontend', 'Forgot password?'), ['/activeuser/account/restore'], ['tabindex' => '5']) ?>
                </p>
            <?php endif; ?>
            <?php if ($model->module->enableRegistration): ?>
                <p class="login-register">
                    <?= Html::a(Yii::t('activeuser_frontend', "Don't have an account? Sign up!"), ['/activeuser/account/register']) ?>
                </p>
            <?php endif ?>
            <?php if ($model->module->enableConfirmation): ?>
                <p class="login-resend">
                    <?= Html::a(Yii::t('activeuser_frontend', "Didn't receive confirmation message?"), ['/activeuser/account/resend']) ?>
                </p>
            <?php endif ?>
        </div>
    </div>
</div>
