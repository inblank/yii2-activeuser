<?php

use inblank\activeuser\models\forms\LoginForm;
use tests\codeception\_fixtures\ProfileFixture;
use tests\codeception\_fixtures\UserFixture;

class LoginCest
{
    public function _before(FunctionalTester $I)
    {
        $I->haveFixtures([
            'users' => UserFixture::className(),
            'profiles' => ProfileFixture::className(),
        ]);
    }

    public function _after(FunctionalTester $I)
    {
    }

//    /**
//     * @inheritdoc
//     */
//    public function _fixtures()
//    {
//        return [
//            'users' => UserFixture::className(),
//            'profiles' => ProfileFixture::className(),
//        ];
//    }

    // tests
    public function testFullLogin(FunctionalTester $I)
    {
        $formName = Yii::createObject(LoginForm::className())->formName();
        $emailField = "{$formName}[email]";
        $passwordField = "{$formName}[password]";

        $I->wantTo('ensure that login works');

        $I->amOnRoute('/activeuser/account/login');

        $I->dontSee('Logout');
        $I->canSeeElement("[name='{$emailField}']");

        $I->amGoingTo('try to login with empty credentials');
        $I->submitForm('form', [
            $emailField => '',
            $passwordField => '',
        ]);

        $I->expectTo('see validations errors');
        $I->see('Email cannot be blank.');
        $I->see('Password cannot be blank.');

        $I->amGoingTo('try to login with not registered email');
        $I->submitForm('form', [
            $emailField => 'not_registered@example.com',
            $passwordField => 'not_registered',
        ]);
        $I->expectTo('see validations errors');
        $I->see('Invalid email or password');

        $I->amGoingTo('try to login with blocked account');
        $user = $I->grabFixture('users', 'blocked');
        $I->submitForm('form', [
            $emailField => $user->email,
            $passwordField => 'blocked',
        ]);
        $I->see('Your account has been blocked');

        $I->amGoingTo('try to login with unconfirmed account');
        $user = $I->grabFixture('users', 'unconfirmed');
        $I->submitForm('form', [
            $emailField => $user->email,
            $passwordField => 'unconfirmed',
        ]);
        $I->see('You need to confirm your email address');

        $user = $I->grabFixture('users', 'active');
        $I->amGoingTo('try to login with wrong credentials');
        $I->submitForm('form', [
            $emailField => $user->email,
            $passwordField => 'wrong',
        ]);
        $I->expectTo('see validations errors');
        $I->see('Invalid email or password');

        $I->amGoingTo('try to login with correct credentials');
        $I->submitForm('form', [
            $emailField => $user->email,
            $passwordField => 'active',
        ]);
        $I->see('Logout');
    }
}
