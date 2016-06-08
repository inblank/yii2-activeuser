<?php
/**
 * @var \inblank\activeuser\models\forms\ResendForm $model
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('activeuser_frontend', 'Resend confirm message');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div id="form-resend" class="panel panel-warning">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="panel-body">
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

                <?= Html::submitButton(Yii::t('activeuser_frontend', 'Resend message'), [
                    'class' => 'btn btn-warning btn-block', 'tabindex' => '2'
                ]) ?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <div class="text-center">
            <p class="activeuser-login">
                <?= Html::a(Yii::t('activeuser_frontend', 'Sign in'), ['/activeuser/account/login'], ['tabindex' => '3']) ?>
            </p>
            <?php if ($model->module->enableRegistration): ?>
                <p class="activeuser-register">
                    <?= Html::a(Yii::t('activeuser_frontend', "Don't have an account? Sign up!"), ['/activeuser/account/register'], ['tabindex' => '4']) ?>
                </p>
            <?php endif ?>
            <?php if ($model->module->enablePasswordRestore): ?>
                <p class="activeuser-recovery">
                    <?= Html::a(Yii::t('activeuser_frontend', 'Forgot password?'), ['/activeuser/account/recovery'], ['tabindex' => '5']) ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>
