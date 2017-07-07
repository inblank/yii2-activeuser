<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model inblank\activeuser\models\User */

$this->title = Yii::t('activeuser_backend', 'Create User');
$this->params['breadcrumbs'][] = ['label' => Yii::t('activeuser_general', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
