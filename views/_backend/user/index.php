<?php

use inblank\activeuser\models\User;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel inblank\activeuser\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('activeuser_general', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('activeuser_backend', 'Create User'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'avatar',
                'label' => false,
                'value' => 'imageUrl',
                'format' => ['image', ['width' => 40]],
                'headerOptions' => [
                    'width' => '1%'
                ]
            ],
            [
                'attribute' => 'gender',
                'value' => 'genderText',
                'filter' => User::gendersList(),
                'headerOptions' => [
                    'width' => '9%'
                ]
            ],
            [
                'attribute' => 'status',
                'value' => 'statusText',
                'filter' => User::statusesList(),
                'headerOptions' => [
                    'width' => '9%'
                ]
            ],
            'email:email',
            'name',
            'birth:date',
            'registered_at:date',

            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                    'delete' => function ($model) {
                        /** @var User $model */
                        return !$model->isConfirmed();
                    }
                ]
            ],
        ],
    ]); ?>
</div>
