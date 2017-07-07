<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model inblank\activeuser\models\User */

$this->title = Yii::t('activeuser_general', 'User') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('activeuser_general', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?><br/>
        <small><?= Html::mailto($model->email) ?></small>
    </h1>

    <p>
        <?= Html::a(Yii::t('activeuser_backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php if (!$model->isBlocked()): ?>
            <?= Html::a(Yii::t('activeuser_backend', 'Block'), ['block', 'id' => $model->id], [
                'class' => 'btn btn-warning',
                'data' => [
                    'confirm' => Yii::t('activeuser_backend', 'Are you sure you want to block user?'),
                    'method' => 'post',
                ],
            ]) ?>
        <?php else: ?>
            <?= Html::a(Yii::t('activeuser_backend', 'Unblock'), ['unblock', 'id' => $model->id], [
                'class' => 'btn btn-warning',
                'data' => [
                    'confirm' => Yii::t('activeuser_backend', 'Are you sure you want to unblock user?'),
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
        <?php if (!$model->isConfirmed()): ?>
            <?= Html::a(Yii::t('activeuser_backend', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('activeuser_backend', 'Are you sure you want to delete user?'),
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
    </p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'imageUrl:image',
            'id',
            'statusText',
            'email:email',
            'name',
            'genderText',
            'birth:date',
            'registered_at:date',
        ],
    ]) ?>

</div>
