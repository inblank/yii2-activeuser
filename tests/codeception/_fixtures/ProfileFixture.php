<?php
namespace tests\codeception\_fixtures;

class ProfileFixture extends \yii\test\ActiveFixture{
    public $modelClass = 'inblank\activeuser\models\Profile';
    public $dataFile = '@tests/codeception/_fixtures/data/profile.php';
}
