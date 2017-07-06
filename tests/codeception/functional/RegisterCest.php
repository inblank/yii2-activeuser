<?php


use inblank\activeuser\models\forms\RegisterForm;
use inblank\activeuser\models\User;
use tests\codeception\_fixtures\ProfileFixture;
use tests\codeception\_fixtures\UserFixture;

class RegisterCest
{
    public $formName;
    public $nameField;
    public $emailField;
    public $passwordField;
    public $route = '/activeuser/account/register';

    public function _before(FunctionalTester $I)
    {
        $I->haveFixtures([
            'users' => UserFixture::className(),
            'profiles' => ProfileFixture::className(),
        ]);

        $this->formName = Yii::createObject(RegisterForm::className())->formName();
        $this->nameField = "{$this->formName}[name]";
        $this->emailField = "{$this->formName}[email]";
        $this->passwordField = "{$this->formName}[password]";
    }

    public function _after(FunctionalTester $I)
    {
    }

    /**
     * @inheritdoc
     */
    public function _fixtures()
    {
        return [
            'users' => UserFixture::className(),
            'profiles' => ProfileFixture::className(),
        ];
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

        $I->amOnRoute($this->route);

        $I->expectTo('see message about disabled registration');
        $I->see('Registration is disabled');

        $I->dontSeeElement("[name='{$this->emailField}']");
        $I->dontSeeElement("[name='{$this->passwordField}']");
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

        $I->amOnRoute($this->route);

        $I->expectTo('see at email fields');
        $I->seeElement("[name='{$this->emailField}']");
        $I->dontSeeElement("[name='{$this->nameField}']");
        $I->dontSeeElement("[name='{$this->passwordField}']");

        $I->amGoingTo('try to register with empty email');
        $I->submitForm('form', [
            $this->emailField => '',
        ]);
        $I->expectTo('see validations errors');
        $I->see('Email cannot be blank');

        $I->amGoingTo('try to register wrong email');
        $I->submitForm('form', [
            $this->emailField => 'qwerty',
        ]);
        $I->expectTo('see validations errors');
        $I->see('Email is not a valid email address');

        $I->amGoingTo('try to register with not unique email');
        $I->submitForm('form', [
            $this->emailField => 'active@example.com',
        ]);
        $I->expectTo('see validations errors');
        $I->see('Email "active@example.com" has already been taken');

        $I->amGoingTo('try to register with new correct email');
        $I->submitForm('form', [
            $this->emailField => $registerEmail,
        ]);
        $I->expectTo('see instruction page');
        $I->see('Check your email');
        $I->seeRecord(User::className(), ['email' => $registerEmail]);
        $I->seeEmailIsSent();

        $I->amGoingTo('try to confirmation page with token from email');
        /** @var \yii\swiftmailer\Message $message */
        $message = $I->grabLastSentEmail();
        $url = $this->getUrlFromEmail('confirm', $message->toString());

        expect("in email must be confirm url", $url)->notEmpty();
        $I->amOnPage($url);
        $I->expectTo('see confirmation page');
        $I->see('Your email was successful confirmed');
        $I->seeEmailIsSent();
        $message = $I->grabLastSentEmail();
        expect("we can fetch thank email", strtolower($message->getSubject()))->contains('thank you');

        $user = User::findOne(['email' => $registerEmail]);
        expect("user must be active", !empty($user) && $user->isActive())->true();

        $I->amGoingTo('try to confirmation page with wrong token');
        $I->amOnPage(['/activeuser/account/confirm', 'token' => '123']);
        $I->expectTo('see wrong confirmation page');
        $I->see('Your email not confirmed');

        $I->amGoingTo('try to confirmation page with empty token');
        $I->amOnPage(['/activeuser/account/confirm']);
        $I->expectTo('see wrong confirmation page');
        $I->see('Your email not confirmed');
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
        $I->amOnRoute($this->route);
        $I->expectTo('see at email fields');
        $I->seeElement("[name='{$this->emailField}']");
        $I->seeElement("[name='{$this->passwordField}']");
        $I->dontSeeElement("[name='{$this->nameField}']");

        $I->amGoingTo('try to register empty password');
        $I->submitForm('form', [
            $this->emailField => $registerEmail,
        ]);
        $I->expectTo('see validations errors');
        $I->see('Password cannot be blank');

        $I->amGoingTo('try to register with too short password');
        $I->submitForm('form', [
            $this->emailField => $registerEmail,
            $this->passwordField => 'qwe'
        ]);
        $I->expectTo('see validations errors');
        $I->see('Password should contain at least 6 characters');

        $I->amGoingTo('try to register with too long password');
        $I->submitForm('form', [
            $this->emailField => $registerEmail,
            $this->passwordField => str_pad('', 50, 'q')
        ]);
        $I->expectTo('see validations errors');
        $I->see('Password should contain at most 20 characters');

        $I->amGoingTo('try to register with correct data');
        $I->submitForm('form', [
            $this->emailField => $registerEmail,
            $this->passwordField => 'qwer12345'
        ]);
        $I->expectTo('see instruction page');
        $I->see('Check your email');
        $I->seeEmailIsSent();
    }

    public function testRegisterWithEmailAndName(FunctionalTester $I)
    {
        $I->changeModuleParams([
            'enableRegistration' => true,
            'registrationFields' => ['name'],
        ]);

        $registerEmail = 'emailname@example.com';

        $I->amGoingTo('register page with email and name fields');
        $I->amOnRoute($this->route);
        $I->expectTo('see at email and name fields');
        $I->seeElement("[name='{$this->emailField}']");
        $I->seeElement("[name='{$this->nameField}']");
        $I->dontSeeElement("[name='{$this->passwordField}']");

        $I->amGoingTo('try to register empty name');
        $I->submitForm('form', [
            $this->emailField => $registerEmail,
        ]);
        $I->expectTo('see validations errors');
        $I->see('Name cannot be blank');

        $I->amGoingTo('try to register with correct data');
        $I->submitForm('form', [
            $this->emailField => $registerEmail,
            $this->nameField => 'Pavel'
        ]);
        $I->expectTo('see instruction page');
        $I->see('Check your email');
        $I->seeEmailIsSent();
    }

    public function testRegisterWithEmailNameAndPassword(FunctionalTester $I)
    {
        $I->changeModuleParams([
            'enableRegistration' => true,
            'registrationFields' => ['name', 'password'],
        ]);

        $registerEmail = 'emailnamepassword@example.com';

        $I->amGoingTo('register page with email, name and password fields');
        $I->amOnRoute($this->route);
        $I->expectTo('see at email, name and password fields');
        $I->seeElement("[name='{$this->emailField}']");
        $I->seeElement("[name='{$this->nameField}']");
        $I->seeElement("[name='{$this->passwordField}']");

        $I->amGoingTo('try to register empty name and password');
        $I->submitForm('form', [
            $this->emailField => $registerEmail,
        ]);
        $I->expectTo('see validations errors');
        $I->see('Password cannot be blank');
        $I->see('Name cannot be blank');

        $I->amGoingTo('try to register empty name');
        $I->submitForm('form', [
            $this->emailField => $registerEmail,
            $this->passwordField => 'testing',
            $this->nameField => '',
        ]);
        $I->expectTo('see validations errors');
        $I->see('Name cannot be blank');
        $I->dontSee('Password cannot be blank');

        $I->amGoingTo('try to register empty password');
        $I->submitForm('form', [
            $this->emailField => $registerEmail,
            $this->nameField => 'Pavel',
            $this->passwordField => ''
        ]);
        $I->expectTo('see validations errors');
        $I->see('Password cannot be blank');
        $I->dontSee('Name cannot be blank');

        $I->amGoingTo('try to register with correct data');
        $I->submitForm('form', [
            $this->emailField => $registerEmail,
            $this->nameField => 'Pavel',
            $this->passwordField => 'testing',
        ]);
        $I->expectTo('see instruction page');
        $I->see('Check your email');
        $I->seeEmailIsSent();
    }

    protected function getUrlFromEmail($linkType, $messageText)
    {
        preg_match_all("/href.+\"(.*)\">/imsU", $messageText, $m);
        foreach ($m[1] as $url) {
            $url = htmlspecialchars_decode(rawurldecode(quoted_printable_decode($url)));
            parse_str(parse_url($url, PHP_URL_QUERY), $url);
            if (!empty($url['r']) && strpos($url['r'], $linkType) !== false) {
                $url[0] = '/' . $url['r'];
                unset($url['r']);
                return $url;
            }
        }
        return false;
    }
}
