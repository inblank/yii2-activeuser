<?php
use yii\helpers\Html;

/**
 * @var \inblank\activeuser\models\User $user user model
 */
$this->title = Yii::t('activeuser_frontend', 'Confirm');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div id="form-resend" class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="panel-body">
                <p class="text-center">
                    <?= Yii::t('activeuser_frontend', 'Your email was successful confirmed', [
                        'email' => $user->email
                    ]) ?>
                </p>
            </div>
        </div>
    </div>
</div>
