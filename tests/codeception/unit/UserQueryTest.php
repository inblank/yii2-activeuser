<?php

namespace tests\codeception;

use Codeception\Specify;
use inblank\activeuser\models\User;
use inblank\activeuser\Module;
use tests\codeception\_fixtures\ProfileFixture;
use tests\codeception\_fixtures\UserFixture;
use yii;
use yii\codeception\TestCase;
use yii\db\Query;

/**
 * Class UserQueryTest
 *
 * @package tests\codeception
 */
class UserQueryTest extends TestCase
{
    use Specify;

    /** @var  Module */
    protected $module;

    /**
     * @inheritdoc
     */
    public function fixtures()
    {
        return [
            'user' => UserFixture::className(),
            'profile' => ProfileFixture::className(),
        ];
    }

    public function testFind()
    {
        expect("we can find user", User::findOne(1))->notNull();
        expect("we can't find wrong user", User::findOne(9999))->null();

        foreach ([User::STATUS_ACTIVE, User::STATUS_BLOCKED, User::STATUS_CONFIRM, User::STATUS_RESTORE] as $status) {
            $realCount = (new Query)->from(User::tableName())
                ->where('[[status]]=:s', [':s' => $status])
                ->count('*', User::getDb());
            $query = User::find();
            switch ($status) {
                case User::STATUS_ACTIVE:
                    $query->active();
                    break;
                case User::STATUS_BLOCKED:
                    $query->blocked();
                    break;
                case User::STATUS_CONFIRM:
                    $query->notConfirmed();
                    break;
                case User::STATUS_RESTORE:
                    $query->restoring();
                    break;
            }
            $users = $query->all();
            expect("result must be as array", is_array($users))->true();
            expect("count of active user must equals", count($users))->equals($realCount);
            if (!empty($users)) {
                expect("result must contain User objects", $users[0])->isInstanceOf(User::className());
            }
        }
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    protected function setUp()
    {
        parent::setUp();
        $this->module = Yii::$app->modules['activeuser'];
    }

}
