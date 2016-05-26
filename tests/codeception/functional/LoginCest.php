<?php


use tests\codeception\_pages\LoginPage;

class LoginCest
{
    public function _before(FunctionalTester $I)
    {
    }

    public function _after(FunctionalTester $I)
    {
    }

    // tests
    public function testFullLogin(FunctionalTester $I)
    {
        $I->wantTo('ensure that login works');

        $page = LoginPage::openBy($I);
        $I->see('Login');

        $I->amGoingTo('try to login with empty credentials');
        $page->login('', '');
        $I->expectTo('see validations errors');
        $I->see('Email cannot be blank.');
        $I->see('Password cannot be blank.');

        $I->amGoingTo('try to login with not registered email');
        $page->login('not_registered@example.com', 'not_registered');
        $I->expectTo('see validations errors');
        $I->see('Invalid email or password');

        $I->amGoingTo('try to login with blocked account');
        $user = $I->getFixture('user')->getModel('blocked');
        $page->login($user->email, 'blocked');
        $I->see('Your account has been blocked');

        $I->amGoingTo('try to login with unconfirmed account');
        $user = $I->getFixture('user')->getModel('unconfirmed');
        $page->login($user->email, 'unconfirmed');
        $I->see('You need to confirm your email address');

        $user = $I->getFixture('user')->getModel('active');
        $I->amGoingTo('try to login with wrong credentials');
        $page->login($user->email, 'wrong');
        $I->expectTo('see validations errors');
        $I->see('Invalid email or password');

        $I->amGoingTo('try to login with correct credentials');
        $page->login($user->email, 'active');
        $I->dontSee('Login');
        $I->see('Logout');
    }
}
