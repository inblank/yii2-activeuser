<?php
/**
 * @var \inblank\activeuser\models\User $user user data
 */

use yii\helpers\Html;

$this->title = $user->name;
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-xs-12 col-md-2">
        <div class="thumbnail">
            <?= Html::img($user->imageUrl, ['alt' => $user->name, 'title' => $user->name]); ?>
        </div>
    </div>
    <div class="col-xs-12 col-md-10">
        <h3><?= $user->name ?></h3>
        <p><?= Html::mailto($user->email) ?></p>
        <p><?= Yii::$app->formatter->asDate($user->registered_at) ?></p>
    </div>
</div>