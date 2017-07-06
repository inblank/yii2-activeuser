<?php
/**
 * @var \inblank\activeuser\models\forms\RegisterForm $model
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('activeuser_frontend', 'Register');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div id="form-register" class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                    'enableClientScript' => false,
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

                <?php if($model->module->isFieldForRegister('name')):?>
                <?= $form->field($model, 'name', [
                    'inputOptions' => [
                        'class' => 'form-control',
                        'tabindex' => '2'
                    ]
                ])?>
                <?php endif;?>

                <?php if($model->module->isFieldForRegister('password')):?>
                <?= $form->field($model, 'password', [
                    'inputOptions' => [
                        'class' => 'form-control',
                        'tabindex' => '3'
                    ]
                ])->passwordInput()?>
                <?php endif;?>

                <?= Html::submitButton(Yii::t('activeuser_frontend', 'Register'), [
                    'class' => 'btn btn-success btn-block', 'tabindex' => '4'
                ]) ?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <div class="text-center">
            <p class="activeuser-login">
                <?= Html::a(Yii::t('activeuser_frontend', 'Already register? Sign in!'), ['/activeuser/account/login'], ['tabindex' => '4']) ?>
            </p>
            <?php if ($model->module->enablePasswordRestore): ?>
                <p class="activeuser-restore">
                    <?= Html::a(Yii::t('activeuser_frontend', 'Forgot password?'), ['/activeuser/account/restore'], ['tabindex' => '5']) ?>
                </p>
            <?php endif; ?>
            <?php if ($model->module->enableConfirmation): ?>
                <p class="activeuser-resend">
                    <?= Html::a(Yii::t('activeuser_frontend', "Didn't receive confirmation message?"), ['/activeuser/account/resend']) ?>
                </p>
            <?php endif ?>
        </div>
    </div>
</div>
