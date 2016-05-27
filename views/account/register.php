<?php
/**
 * @var \inblank\activeuser\models\forms\RegisterForm $model
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('activeuser_general', 'Register');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="registerpanel">
    <div class="registerpanel__header">
        <h3 class="registerpanel__title"><?= Html::encode($this->title) ?></h3>
    </div>
    <div class="registerpanel__body">
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

        <?= Html::submitButton(Yii::t('activeuser_general', 'Register'), [
            'class' => 'button', 'tabindex' => '4'
        ]) ?>

        <?php if ($model->module->enablePasswordRestore): ?>
        <?php endif; ?>

        <?php ActiveForm::end(); ?>
    </div>
    <div class="regiserpanel__links">
        <div class="registerpanel__login">
            <?= Html::a(Yii::t('activeuser_general', 'Already register? Sign in!'), ['/activeuser/account/login'], ['tabindex' => '4']) ?>
        </div>
        <?php if ($model->module->enablePasswordRestore): ?>
            <div class="registerpanel__recovery">
                <?= Html::a(Yii::t('activeuser_general', 'Forgot password?'), ['/activeuser/account/recovery'], ['tabindex' => '5']) ?>
            </div>
        <?php endif; ?>
        <?php if ($model->module->enableConfirmation): ?>
            <div class="registerpanel__resend">
                <?= Html::a(Yii::t('activeuser_general', "Didn't receive confirmation message?"), ['/activeuser/account/resend']) ?>
            </div>
        <?php endif ?>
    </div>
</div>
