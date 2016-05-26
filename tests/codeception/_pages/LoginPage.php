<?php

namespace tests\codeception\_pages;

use inblank\activeuser\models\forms\LoginForm;
use yii;
use yii\codeception\BasePage;

/**
 * Represents login page.
 *
 * @property \FunctionalTester $actor
 */
class LoginPage extends BasePage
{
    /** @inheritdoc */
    public $route = '/activeuser/account/login';

    /**
     * Login by full form
     * @param string $email email for login
     * @param string $password password for login
     */
    public function login($email, $password)
    {
        /** @var LoginForm $loginForm */
        $formName = strtolower(Yii::createObject(LoginForm::className())->formName());
        $this->actor->fillField('#'.$formName.'-email', $email);
        $this->actor->fillField('#'.$formName.'-password', $password);
        $this->actor->click('Sign in');
    }
}
