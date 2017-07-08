<?php
/**
 * @var \inblank\activeuser\models\User $user user
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('activeuser_frontend', 'Update');
$this->params['breadcrumbs'][] = ['label' => $user->name, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="user-update">

    <h1><?= Yii::t('activeuser_frontend', 'Update') ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($user, 'avatar')->widget(\inblank\image\ImageUploadWidget::className()) ?>

    <?= $form->field($user, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($user, 'gender')->dropDownList(\inblank\activeuser\models\User::gendersList()) ?>

    <?= $form->field($user, 'birth')->textInput(['placeholder' => Yii::t('activeuser_frontend', 'YYYY-MM-DD')]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('activeuser_frontend', 'Update'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('activeuser_frontend', 'Cancel'), ['view'], ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
