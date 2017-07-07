<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model inblank\activeuser\models\User */

$this->title = Yii::t('activeuser_backend', 'Update User') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('activeuser_general', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('activeuser_backend', 'Update');
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?><br/>
        <small><?= Html::mailto($model->email) ?></small>
    </h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
