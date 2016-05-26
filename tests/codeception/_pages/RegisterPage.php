<?php

namespace tests\codeception\_pages;

use inblank\activeuser\models\forms\RegisterForm;
use yii;
use yii\codeception\BasePage;

/**
 * Represents register page.
 *
 * @property \FunctionalTester $actor
 */
class RegisterPage extends BasePage
{
    /** @inheritdoc */
    public $route = '/activeuser/account/register';

    /**
     * Register
     * @param string $email email for register
     * @param string $name name for register
     */
    public function register($email, $name)
    {
        $formName = strtolower(Yii::createObject(RegisterForm::className())->formName());
        $this->actor->fillField('#'.$formName.'-email', $email);
        $this->actor->fillField('#'.$formName.'-name', $name);
        $this->actor->click('Register');
    }
}
