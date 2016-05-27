<?php

namespace tests\codeception;

use app\components\MailMock;
use Codeception\Specify;
use inblank\activeuser\models\Profile;
use inblank\activeuser\models\User;
use inblank\activeuser\Module;
use tests\codeception\_fixtures\ProfileFixture;
use tests\codeception\_fixtures\UserFixture;
use yii;
use yii\codeception\TestCase;

class UserTest extends TestCase
{
    use Specify;

    /** @var  Module */
    public $module;

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

    /**
     * Registration test with `null` data for User model
     */
    public function testRegistrationNull()
    {
        $this->module = Yii::$app->getModule('activeuser');
        $this->module->enableRegistration = true;
        $this->module->enableConfirmation = true;
        $this->module->registrationFields = [];

        $this->specify("we have register with null data", function () {
            $user = new User([
                'status' => null,
                'email' => 'user@example.com',
                'pass_hash' => null,
                'name' => null,
                'gender' => null,
                'birth' => null,
                'avatar' => null,
                'access_token' => null,
                'auth_key' => null,
                'token' => null,
                'token_created_at' => null,
                'registered_at' => null,
            ]);
            expect("we can register user with correct email and null data", $user->register())->true();
        });
    }

    /**
     * Registration test for User model
     */
    public function testRegistration()
    {
        $this->module = Yii::$app->getModule('activeuser');
        $this->module->enableRegistration = false;
        $this->specify("we have register if registration disabled", function () {
            $user = new User([
                'email' => 'user@example.com',
                'password' => 'password',
                'name' => 'Tester',
            ]);
            expect("we can't register if registration disabled", $user->register())->false();
            expect("we must see message about this", $user->getErrors())->hasKey('registration');
        });

        $this->module->enableRegistration = true;
        $this->module->enableConfirmation = true;
        $this->module->registrationFields = [];
        $this->specify("we have register user by email", function () {
            $user = new User([
                'email' => 'user@example.com',
            ]);
            expect("we can register user by email only", $user->register())->true();
            expect("user must have register date", $user->registered_at)->notEmpty();
            expect("user must have status STATUS_CONFIRM", $user->status)->equals(User::STATUS_CONFIRM);
            expect("we can get user profile", Profile::findOne(['user_id' => $user->id]))->notNull();

            expect("we must view confirmation email", MailMock::$mails[0]['to'])->equals($user->email);
            expect("we must view confirmation email", MailMock::$mails[0]['subject'])->contains('confirm');

            //register() on existing user throw \RuntimeException
            $user->register();
        }, ['throws' => new \RuntimeException]);

        $this->specify("we have register user with same email", function () {
            $user = new User([
                'email' => 'user@example.com',
            ]);
            expect("we can't create user with same email", $user->register())->false();
            expect("we must see error message", $user->getErrors())->hasKey('email');
        });

        $this->module->registrationFields = ['password'];
        $this->specify("we have register user by email with password", function () {
            $pass = 'password';
            $user = new User([
                'email' => 'user1@example.com',
            ]);
            expect("we can't register user without pass", $user->register())->false();
            expect('we must see error in password', $user->getErrors())->hasKey('password');
            $user->password = $pass;
            expect("we can register user", $user->register())->true();
            expect("`pass_hash` must match", Yii::$app->security->validatePassword($pass, $user->pass_hash))->true();
            expect("we can get user profile", Profile::findOne(['user_id' => $user->id]))->notNull();
        });

        $this->module->registrationFields = ['password', 'name'];
        $this->specify("we have register user with full data", function () {
            $pass = 'password';
            $name = 'Tester';
            $user = new User([
                'email' => 'user2@example.com',
            ]);
            expect("we can't register user without pass and name", $user->register())->false();
            expect('we must see error in password', $user->getErrors())->hasKey('password');
            expect('we must see error in name', $user->getErrors())->hasKey('name');
            $user->password = $pass;
            $user->name = $name;
            expect("we can register user", $user->register())->true();
            expect("`pass_hash` must match", Yii::$app->security->validatePassword($pass, $user->pass_hash))->true();
            expect("we can get user profile", Profile::findOne(['user_id' => $user->id]))->notNull();
            $user1 = User::findOne($user->id);
            expect("name must be set", $user1->name)->equals($name);
        });

        $this->module->registrationFields = ['password', 'name', 'gender', 'birth'];
        $this->specify("we have register user with full data", function () {
            $pass = 'password';
            $name = 'Tester';
            $gender = User::MALE;
            $birth = '2000-01-01';
            $user = new User([
                'email' => 'user3@example.com',
                'password' => $pass,
                'name' => $name,
            ]);
            expect("we can register user without gender and birth", $user->register())->true();

            $user = new User([
                'email' => 'user4@example.com',
                'password' => $pass,
                'name' => $name,
                'gender' => $gender,
                'birth' => $birth,
            ]);
            expect("we can register user with full data", $user->register())->true();
            $user1 = User::findOne($user->id);
            expect('we must see gender', $user1->gender)->equals($gender);
            expect('we must see birth', $user1->birth)->equals($birth);
        });

        $this->module->enableConfirmation = false;
        $this->module->registrationFields = [];
        MailMock::$mails = [];
        $this->specify("we have register user without confirm email", function () {
            $user = new User([
                'email' => 'user5@example.com',
            ]);
            expect("we can register user", $user->register())->true();
            expect("user must have status STATUS_ACTIVE", $user->status)->equals(User::STATUS_ACTIVE);
            expect("we must view confirmation email", MailMock::$mails[0]['to'])->equals($user->email);
            expect("we must view confirmation email", MailMock::$mails[0]['subject'])->contains('register');
        });
    }

    public function testConfirm()
    {
        $this->module = Yii::$app->getModule('activeuser');

        $this->module->enableRegistration = true;
        $this->module->enableConfirmation = true;
        $this->module->registrationFields = [];

        $this->specify("we have register and confirm user", function () {
            $user = new User([
                'email' => 'user@example.com',
            ]);

            expect('we can register user', $user->register())->true();
            expect('we must see status STATUS_CONFIRM', $user->status)->equals(User::STATUS_CONFIRM);

            MailMock::$mails = [];
            $confirmedUser = User::findByToken($user->token);
            expect('we can confirm user', $confirmedUser->confirm())->true();
            expect('confirm user must be equal registered user', $confirmedUser->id)->equals($user->id);
            expect("we must view register email", MailMock::$mails[0]['to'])->equals($user->email);
            expect("we must view register email", MailMock::$mails[0]['subject'])->contains('register');

            expect('we cannot confirm already confirmed user', $confirmedUser->confirm())->false();
            expect('we must see error key `error`', $confirmedUser->getErrors())->hasKey('error');
        });

        $this->specify("we have register and try confirm user with expired token", function () {
            $user = new User([
                'email' => 'user1@example.com',
            ]);
            expect('we can register user', $user->register())->true();
            $user->updateAttributes([
                'token_created_at' => time() - 60 * 60 * 24 * 1000,
            ]);
            expect("we can't confirm user with expired token", $user->confirm())->false();
            expect('we must see error key `token`', $user->getErrors())->hasKey('token');
        });
    }

    public function testRestore()
    {
        $this->specify("we have register and confirm user", function () {
            /** @var Module $module */
            $module = Yii::$app->modules['activeuser'];

            $module->enableRegistration = true;
            $module->enableConfirmation = true;
            $module->registrationFields = [];

            $user = new User([
                'email' => 'user@example.com',
            ]);

            expect('we can register user', $user->register())->true();
            expect('we cannot request restore on unconfirmed user', $user->restore())->false();
            expect('we must see `error` error', $user->getErrors())->hasKey('error');
            expect('we can confirm user', $user->confirm())->true();

            $module->enablePasswordRestore = false;
            expect("we can't request restore if disabled", $user->restore())->false();
            expect('we must see `error` error', $user->getErrors())->hasKey('error');

            $module->enablePasswordRestore = true;
            MailMock::$mails = [];
            expect('we can request restore', $user->restore())->true();

            $module->generatePassOnRestore = false;
            $checkedUser = User::findOne($user->id);
            expect('user must have STATUS_RESTORE', $checkedUser->status)->equals(User::STATUS_RESTORE);
            expect('user must have not empty token', $checkedUser->token)->notEmpty();
            expect("we must view restore email", MailMock::$mails[0]['to'])->equals($checkedUser->email);
            expect("we must view restore email", MailMock::$mails[0]['subject'])->contains('restore');

            expect("we cannot change password on empty", $checkedUser->changePassword())->false();
            expect('we must see `password` error', $checkedUser->getErrors())->hasKey('password');

            $checkedUser->updateAttributes([
                'token_created_at' => time() - 60 * 60 * 24 * 1000,
            ]);
            expect("we can't change password user with expired token", $checkedUser->changePassword())->false();
            expect('we must see error key `token`', $checkedUser->getErrors())->hasKey('token');
            $checkedUser->updateAttributes([
                'token_created_at' => time() - 60,
            ]);

            $module->generatePassOnRestore = true;
            $checkedUser->password = '';
            MailMock::$mails = [];
            expect("we can change password by automatically generated", $checkedUser->changePassword())->true();
            $password = $checkedUser->password;
            $user = User::findOne($checkedUser->id);
            expect('user must have STATUS_ACTIVE', $user->status)->equals(User::STATUS_ACTIVE);
            expect('token must be empty', $user->token)->isEmpty();
            expect('password must be changed and equal', Yii::$app->security->validatePassword($password, $user->pass_hash))->true();
            expect("we must view change confirmation email", MailMock::$mails[0]['to'])->equals($user->email);
            expect("we must view change confirmation email", MailMock::$mails[0]['subject'])->contains('changed');

            // test manually changed password
            $user->restore();
            $checkedUser = User::findOne($user->id);
            $module->generatePassOnRestore = true;
            $password = 'qwerty';
            $checkedUser->password = $password;
            expect("we can change password on manually entered if set autogeneration", $checkedUser->changePassword())->true();
            expect('password must be changed and equal', Yii::$app->security->validatePassword($password, $checkedUser->pass_hash))->true();

            $checkedUser->restore();
            $user = User::findOne($checkedUser->id);
            $module->generatePassOnRestore = false;
            $password = '123456';
            $user->password = $password;
            expect("we can change password on manually entered if NOT set autogenerate", $user->changePassword())->true();
            expect('password must be changed and equal', Yii::$app->security->validatePassword($password, $user->pass_hash))->true();

            /** @var User $user */
            $user = $this->getFixture('user')->getModel('active');
            expect("we can't change password if user not restore", $user->changePassword())->false();
            expect('we must see `error` error', $user->getErrors())->hasKey('error');
        });

        $this->specify("try to restore blocked user", function () {
            $user = User::findOne(['status' => User::STATUS_BLOCKED]);
            expect("we must have blocked user", $user)->notNull();
            expect("we cannot restore on blocked user", $user->restore())->false();
            expect('we must see `error`', $user->getErrors())->hasKey('error');
        });
    }

    public function testChecks()
    {
        /** @var User $user */
        $user = $this->getFixture('user')->getModel('blocked');
        expect('user must be blocked', $user->isBlocked())->true();
        expect("user can't be active", $user->isActive())->false();
        expect("user can be confirmed", $user->isConfirmed())->true();
        expect("user can't be restored", $user->isRestore())->false();

        $user = $this->getFixture('user')->getModel('unconfirmed');
        expect('user must be not confirmed', $user->isConfirmed())->false();
        expect("user can't be blocked", $user->isBlocked())->false();
        expect("user can't be active", $user->isActive())->false();
        expect("user can't be restored", $user->isRestore())->false();

        $user = $this->getFixture('user')->getModel('active');
        expect('user must be active', $user->isActive())->true();
        expect("user can't be blocked", $user->isBlocked())->false();
        expect("user can be confirmed", $user->isConfirmed())->true();
        expect("user can't be restored", $user->isRestore())->false();

        $user = $this->getFixture('user')->getModel('restore');
        expect('user must be restore', $user->isRestore())->true();
        expect("user can't be active", $user->isActive())->false();
        expect("user can't be blocked", $user->isBlocked())->false();
        expect("user can be confirmed", $user->isConfirmed())->true();
    }

    public function testGettersFinders()
    {
        /** @var User $user */
        $user = $this->getFixture('user')->getModel('active');
        $accessTokens = ['active' => $user->access_token];
        expect("id getter must be equals direct access", $user->getId())->equals($user->id);
        expect("auth_key getter must be equals direct access", $user->getAuthKey())->equals($user->auth_key);

        expect("wrong auth_key cannot be tested", $user->validateAuthKey('wrong_key'))->false();
        expect("empty auth_key cannot be tested", $user->validateAuthKey(''))->false();
        expect("correct auth_key can be tested", $user->validateAuthKey($user->auth_key))->true();

        $user = $this->getFixture('user')->getModel('blocked');
        $accessTokens['blocked'] = $user->access_token;
        expect("user getProfile() must return query", $user->getProfile())->isInstanceOf(yii\db\ActiveQuery::className());
        expect("we can get blocked user profile", $user->profile)->notEmpty();
        expect("blocked user can't be validated by auth_key", $user->validateAuthKey($user->auth_key))->false();
        $user = $this->getFixture('user')->getModel('unconfirmed');
        $accessTokens['unconfirmed'] = $user->access_token;
        expect("we can get unconfirmed user profile", $user->getProfile()->one())->notEmpty();
        expect("unconfirmed user can't be validated by auth_key", $user->validateAuthKey($user->auth_key))->false();
        $user = $this->getFixture('user')->getModel('emptyauth');
        $accessTokens['emptyauth'] = $user->access_token;
        expect("we can get active user profile", $user->profile)->notEmpty();
        expect("user with empty auth_key can't be validated by auth_key", $user->validateAuthKey(''))->false();

        expect("founded identity must be equals", User::findIdentity($user->id)->toArray())->equals($user->toArray());
        expect("identity cannot be found", User::findIdentity(99999))->isEmpty();

        expect('only active user can be founded by access token', User::findIdentityByAccessToken($accessTokens['active']))->notNull();
        expect('only active user can be founded by access token', User::findIdentityByAccessToken($accessTokens['emptyauth']))->notNull();
        expect('blocked user cannot be founded by access token', User::findIdentityByAccessToken($accessTokens['blocked']))->null();
        expect('unconfirmed user cannot be founded by access token', User::findIdentityByAccessToken($accessTokens['unconfirmed']))->null();
        expect('user with empty access token cannot be founded by access token', User::findIdentityByAccessToken(''))->null();

        expect("we cannot find user by empty token", User::findByToken(''))->null();
        expect("we cannot find user by null-value token", User::findByToken(null))->null();
        expect("we cannot find user by wrong token", User::findByToken('1'))->null();

        // check false before save
        $user = new User([
            'email' => 'eventchecker@example.com',
            'password' => 'pass',
            'name' => 'Name',
        ]);
        $user->on(yii\db\BaseActiveRecord::EVENT_BEFORE_INSERT, function ($event) {
            /** @var yii\base\ModelEvent $event */
            $event->isValid = false;
            $event->sender->addError('event', 'Error event');
        });
        expect("user cannot be saved", $user->save())->false();
        expect("we can see error", $user->getErrors())->hasKey('event');
        expect("password hash must be empty", $user->pass_hash)->isEmpty();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    protected function setUp()
    {
        parent::setUp();
    }

}
