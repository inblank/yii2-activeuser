<?php
namespace tests\codeception\_fixtures;

class UserFixture extends \yii\test\ActiveFixture{
    public $modelClass = 'inblank\activeuser\models\User';
    public $dataFile = '@tests/codeception/_fixtures/data/user.php';
}
