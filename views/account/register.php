<?php
/**
 * @var \inblank\activeuser\models\forms\RegisterForm $model
 * @var \inblank\activeuser\Module $module
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
        <?= $form->field($model, 'name', [
            'inputOptions' => [
                'class' => 'form-control',
                'tabindex' => '2'
            ]
        ])?>

        <?= Html::submitButton(Yii::t('activeuser_general', 'Register'), [
            'class' => 'button', 'tabindex' => '3'
        ]) ?>

        <?php if ($module->enablePasswordRecovery): ?>
            <div class="registerpanel__login">
                <?= Html::a(Yii::t('activeuser_general', 'Already register? Sign in!'), ['/activeuser/account/login'], ['tabindex' => '4']) ?>
            </div>
        <?php endif; ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
