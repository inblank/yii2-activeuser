<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model inblank\activeuser\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'avatar')->widget(\inblank\image\ImageUploadWidget::className()) ?>

    <?php if ($model->isNewRecord):?>
    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true, 'autocomplete'=>'new-password']) ?>
    <?php endif;?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'gender')->dropDownList(\inblank\activeuser\models\User::gendersList()) ?>

    <?= $form->field($model, 'birth')->textInput(['placeholder'=>Yii::t('activeuser_backend', 'YYYY-MM-DD')]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('activeuser_backend', 'Create') : Yii::t('activeuser_backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
