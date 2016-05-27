<?php


use app\components\MailMock;
use inblank\activeuser\models\User;
use tests\codeception\_pages\ConfirmPage;
use tests\codeception\_pages\RegisterPage;

class RegisterCest
{
    public function _before(FunctionalTester $I)
    {
    }

    public function _after(FunctionalTester $I)
    {
    }

    /**
     * Test disabled registration
     * @param FunctionalTester $I
     */
    public function testRegisterDisabled(FunctionalTester $I)
    {
        $I->changeModuleParams([
            'enableRegistration' => false,
        ]);

        /** @var \inblank\activeuser\Module $module */
        $I->wantTo('ensure that register works');

        // turn off registration
        $I->amGoingTo('register page if registration disabled');
        /** @var RegisterPage $page */
        $page = RegisterPage::openBy($I);
        $I->expectTo('see message about disabled registration');
        $I->see('disabled');
        $I->dontSeeElement($page->fullFieldId('email'));
    }

    /**
     * Test registration with only email
     * @param FunctionalTester $I
     */
    public function testRegisterOnlyWithEmail(FunctionalTester $I)
    {
        $I->changeModuleParams([
            'enableRegistration' => true,
            'registrationFields' => [],
        ]);

        $registerEmail = 'onlyemail@example.com';

        $I->amGoingTo('register page with email field');
        /** @var \inblank\activeuser\Module $module */
        /** @var RegisterPage $page */
        $page = RegisterPage::openBy($I);
        $I->expectTo('see at email fields');
        $I->seeElement($page->fullFieldId('email'));
        $I->dontSeeElement($page->fullFieldId('name'));
        $I->dontSeeElement($page->fullFieldId('password'));

        $I->amGoingTo('try to register with empty email');
        $page->register();
        $I->expectTo('see validations errors');
        $I->see('Email cannot be blank');

        $I->amGoingTo('try to register wrong email');
        $page->register(['email' => 'qwerty']);
        $I->expectTo('see validations errors');
        $I->see('Email is not a valid email address');

        $I->amGoingTo('try to register with not unique email');
        $page->register(['email' => 'active@example.com']);
        $I->expectTo('see validations errors');
        $I->see('Email "active@example.com" has already been taken');

        MailMock::$mails = [];
        $I->amGoingTo('try to register with new correct email');
        $page->register(['email' => $registerEmail]);
        $I->expectTo('see instruction page');
        $I->see('Check your email');
        $I->seeRecord(User::className(), ['email'=>$registerEmail]);

        $I->amGoingTo('try to confirmation page with token from email');
        $url = $page->getUrlFromEmail('confirm', \app\components\MailMock::$mails[0]['body']);
        expect("in email must be confirm url", $url)->notEmpty();
        unset($url['r']);
        MailMock::$mails = [];
        ConfirmPage::openBy($I, $url);
        $I->expectTo('see confirmation page');
        $I->see('Email was confirmed successful');
        expect("we can fetch thank email", strtolower(MailMock::$mails[0]['subject']))->contains('thank you');
        $user = User::findOne(['email' => $registerEmail]);
        expect("user must be active", !empty($user) && $user->isActive())->true();

        $I->amGoingTo('try to confirmation page with wrong token');
        ConfirmPage::openBy($I, ['token' => '123']);
        $I->expectTo('see wrong confirmation page');
        $I->see('Confirm not successful');

        $I->amGoingTo('try to confirmation page with empty token');
        ConfirmPage::openBy($I);
        $I->expectTo('see wrong confirmation page');
        $I->see('Confirm not successful');
    }

    /**
     * @param FunctionalTester $I
     */
    public function testRegisterWithEmailAndPassword(FunctionalTester $I)
    {
        $I->changeModuleParams([
            'enableRegistration' => true,
            'registrationFields' => ['password'],
        ]);

        $registerEmail = 'emailpassword@example.com';

        $I->amGoingTo('register page with email and password fields');
        /** @var RegisterPage $page */
        $page = RegisterPage::openBy($I);
        $I->expectTo('see at email fields');
        $I->seeElement($page->fullFieldId('email'));
        $I->seeElement($page->fullFieldId('password'));
        $I->dontSeeElement($page->fullFieldId('name'));

        $I->amGoingTo('try to register empty password');
        $page->register(['email' => $registerEmail]);
        $I->expectTo('see validations errors');
        $I->see('Password cannot be blank');

        $I->amGoingTo('try to register with too short password');
        $page->register([
            'email' => $registerEmail,
            'password' => 'qwe',
        ]);
        $I->expectTo('see validations errors');
        $I->see('Password should contain at least 6 characters');

        $I->amGoingTo('try to register with too long password');
        $page->register([
            'email' => $registerEmail,
            'password' => str_pad('', 50, 'q'),
        ]);
        $I->expectTo('see validations errors');
        $I->see('Password should contain at most 20 characters');

        MailMock::$mails = [];
        $I->amGoingTo('try to register with correct data');
        $page->register([
            'email' => $registerEmail,
            'password' => 'qwer12345',
        ]);
        $I->expectTo('see instruction page');
        $I->see('Check your email');
    }

    public function testRegisterWithEmailAndName(FunctionalTester $I)
    {
        $I->changeModuleParams([
            'enableRegistration' => true,
            'registrationFields' => ['name'],
        ]);

        $registerEmail = 'emailname@example.com';

        $I->amGoingTo('register page with email and name fields');
        /** @var RegisterPage $page */
        $page = RegisterPage::openBy($I);
        $I->expectTo('see at email and name fields');
        $I->seeElement($page->fullFieldId('email'));
        $I->seeElement($page->fullFieldId('name'));
        $I->dontSeeElement($page->fullFieldId('password'));

        $I->amGoingTo('try to register empty name');
        $page->register(['email' => $registerEmail]);
        $I->expectTo('see validations errors');
        $I->see('Name cannot be blank');

        MailMock::$mails = [];
        $I->amGoingTo('try to register with correct data');
        $page->register([
            'email' => $registerEmail,
            'name' => 'Pavel',
        ]);
        $I->expectTo('see instruction page');
        $I->see('Check your email');
    }

    public function testRegisterWithEmailNameAndPassword(FunctionalTester $I)
    {
        $I->changeModuleParams([
            'enableRegistration' => true,
            'registrationFields' => ['name', 'password'],
        ]);

        $registerEmail = 'emailnamepassword@example.com';

        $I->amGoingTo('register page with email, name and password fields');
        /** @var RegisterPage $page */
        $page = RegisterPage::openBy($I);
        $I->expectTo('see at email, name and password fields');
        $I->seeElement($page->fullFieldId('email'));
        $I->seeElement($page->fullFieldId('name'));
        $I->seeElement($page->fullFieldId('password'));

        $I->amGoingTo('try to register empty name and password');
        $page->register(['email' => $registerEmail]);
        $I->expectTo('see validations errors');
        $I->see('Password cannot be blank');
        $I->see('Name cannot be blank');

        $I->amGoingTo('try to register empty name');
        $page->register(['email' => $registerEmail, 'name'=>'', 'password'=>'testing']);
        $I->expectTo('see validations errors');
        $I->see('Name cannot be blank');
        $I->dontSee('Password cannot be blank');

        $I->amGoingTo('try to register empty password');
        $page->register(['email' => $registerEmail, 'name'=>'Pavel', 'password'=>'']);
        $I->expectTo('see validations errors');
        $I->see('Password cannot be blank');
        $I->dontSee('Name cannot be blank');

        MailMock::$mails = [];
        $I->amGoingTo('try to register with correct data');
        $page->register([
            'email' => $registerEmail,
            'name' => 'Pavel',
            'password' => 'testing',
        ]);
        $I->expectTo('see instruction page');
        $I->see('Check your email');
    }

}
