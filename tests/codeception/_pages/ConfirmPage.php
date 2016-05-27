<?php

namespace tests\codeception\_pages;

use yii;
use yii\codeception\BasePage;

/**
 * Represents email confirm page.
 *
 * @property \FunctionalTester $actor
 */
class ConfirmPage extends BasePage
{
    /** @inheritdoc */
    public $route = '/activeuser/account/confirm';
}
