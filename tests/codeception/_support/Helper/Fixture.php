<?php
namespace tests\codeception\_support\Helper;

use Codeception\Module;
use Codeception\TestCase;
use tests\codeception\_fixtures\UserFixture;
use yii\test\FixtureTrait;

class Fixture extends Module
{
    use FixtureTrait;

    /**
     * @var array
     */
    public static $excludeActions = [
        'loadFixtures',
        'unloadFixtures',
        'getFixtures',
        'globalFixtures',
        'fixtures'
    ];

    /**
     * @param TestCase $testcase
     */
    public function _before(TestCase $testcase)
    {
        $this->unloadFixtures();
        $this->loadFixtures();
        parent::_before($testcase);
    }

    /**
     * @param TestCase $testcase
     */
    public function _after(TestCase $testcase)
    {
        $this->unloadFixtures();
        parent::_after($testcase);
    }

    /**
     * @inheritdoc
     */
    public function fixtures()
    {
        return [
            'user' => UserFixture::className(),
        ];
    }
}
