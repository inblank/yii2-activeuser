<?php
use yii\helpers\Html;

/**
 * @var string $email email address
 */
$this->title = Yii::t('activeuser_frontend', 'Resend confirm message');
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
                    <?= Yii::t('activeuser_frontend', 'Confirmation message was sent to {email}', [
                        'email' => $email
                    ]) ?>
                </p>
            </div>
        </div>
    </div>
</div>
