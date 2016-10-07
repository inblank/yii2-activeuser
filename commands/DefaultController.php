<?php

namespace inblank\activeuser\commands;

use inblank\activeuser\components\ConsoleController;
use inblank\activeuser\traits\CommonTrait;
use Yii;
use yii\helpers\Console;

/**
 * Managing user accounts
 * @package inblank\activeuser\commands
 */
class DefaultController extends ConsoleController
{
    use CommonTrait;

    /** @var bool whether to send email about action. If not set use module's settings */
    public $sendEmail = false;

    /** @inheritdoc */
    public function options($actionID)
    {
        return ['sendEmail'];
    }

    /**
     * Create a new user
     * @param string $email user email
     * @param string $name user name
     * @param string|null $password optional user password. If not set will be generated automatically
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate($email = null, $name = null, $password = null)
    {
        /** @var \inblank\activeuser\models\User $user */
        $user = Yii::createObject(self::di('User'));
        $this->getModule()->registrationFields = ['name'];
        $user->setAttributes(compact('email', 'name', 'password'), false);
        if (!$user->validate(['email', 'name'])) {
            $this->showUsage('email,name', 'password', $user->getErrors());
        }
        $nameString = $this->ansiFormat($name, Console::FG_GREEN);
        $emailString = $this->ansiFormat($email, Console::FG_GREEN);
        $this->confirmAction(Yii::t("activeuser_backend", "Create new user {nameString} with email {emailString}?", [
            'emailString' => $emailString,
            'nameString' => $nameString,
        ]));
        // create user
        if (!$user->create($this->sendEmail)) {
            $this->showErrors($user->getErrors());
        }
        $passwordString = $this->ansiFormat($user->password, Console::FG_GREEN);
        $info = Yii::t("activeuser_backend", 'Created user');
        $this->stdout($info . PHP_EOL, Console::BOLD);
        $this->stdout(str_pad('', mb_strlen($info), '-') . PHP_EOL, Console::BOLD);
        $this->stdout(Yii::t('activeuser_general', 'Name') . ': ' . $nameString . PHP_EOL);
        $this->stdout(Yii::t('activeuser_general', 'Email') . ': ' . $emailString . PHP_EOL);
        $this->stdout(Yii::t('activeuser_general', 'Password') . ': ' . $passwordString . PHP_EOL);
    }

    /**
     * Block user
     * @param string $email user email to block
     */
    public function actionBlock($email = null)
    {
        if (empty($email)) {
            $this->showUsage('email');
        }
        $user = $this->findUser($email);
        $nameString = $this->ansiFormat($user->name, Console::FG_GREEN);
        $emailString = $this->ansiFormat($user->email, Console::FG_GREEN);
        if (!$user->isActive()) {
            $this->showErrors([
                'error' => [
                    Yii::t('activeuser_backend', "Can't block user {name} with email {email}", [
                        'name' => $nameString,
                        'email' => $emailString,
                    ])
                ]
            ]);
        }
        $this->confirmAction(Yii::t("activeuser_backend", "Block user {name} with email {email}?", [
            'name' => $nameString,
            'email' => $emailString,
        ]));
        $user->block($this->sendEmail);
        $this->stdout(
            Yii::t('activeuser_backend', 'User {name} with email {email} was blocked', [
                'name' => $nameString,
                'email' => $emailString,
            ]) . PHP_EOL
        );
    }

    /**
     * Unblock user
     * @param string $email user email to unblock
     */
    public function actionUnblock($email = null)
    {
        if (empty($email)) {
            $this->showUsage('email');
        }
        $user = $this->findUser($email);
        $nameString = $this->ansiFormat($user->name, Console::FG_GREEN);
        $emailString = $this->ansiFormat($user->email, Console::FG_GREEN);
        if (!$user->isBlocked()) {
            $this->showErrors([
                'error' => [
                    Yii::t('activeuser_backend', 'User {name} with email {email} not blocked', [
                        'name' => $nameString,
                        'email' => $emailString,
                    ])
                ]
            ]);
        }
        $this->confirmAction(Yii::t("activeuser_backend", 'Unblock user {name} with email {email}?', [
            'name' => $nameString,
            'email' => $emailString,
        ]));
        $user->unblock($this->sendEmail);
        $this->stdout(
            Yii::t('activeuser_backend', 'User {name} with email {email} was unblocked', [
                'name' => $nameString,
                'email' => $emailString,
            ]) . PHP_EOL
        );
    }

    /**
     * Change user password
     * @param string $email user email
     * @param string|null $password new password. If not set will be generated automatically
     */
    public function actionPassword($email = null, $password = null)
    {
        if (empty($email)) {
            $this->showUsage('email', 'password');
        }
        $user = $this->findUser($email);
        $nameString = $this->ansiFormat($user->name, Console::FG_GREEN);
        $emailString = $this->ansiFormat($user->email, Console::FG_GREEN);
        $this->confirmAction(Yii::t("activeuser_backend", 'Change password user {name} with email {email}?', [
            'name' => $nameString,
            'email' => $emailString,
        ]));
        $user->password = $password;
        $user->newPassword($this->sendEmail);
        $passwordString = $this->ansiFormat($user->password, Console::BOLD);
        $info = Yii::t("activeuser_backend", 'New user data');
        $this->stdout($info . PHP_EOL, Console::BOLD);
        $this->stdout(str_pad('', mb_strlen($info), '-') . PHP_EOL);
        $this->stdout(Yii::t('activeuser_general', 'Email') . ': ' . $emailString . PHP_EOL);
        $this->stdout(Yii::t('activeuser_general', 'Password') . ': ' . $passwordString . PHP_EOL);
    }

    /**
     * Find user
     * @param string $email user email for search
     * @return \inblank\activeuser\models\User
     * @throws \yii\base\ExitException
     * @throws \yii\base\InvalidConfigException
     */
    protected function findUser($email)
    {
        $user = Yii::createObject(self::di('User'))->findOne(['email' => $email]);
        if ($user !== null) {
            return $user;
        }
        return $this->showErrors([
            'user' => [
                Yii::t('activeuser_backend', 'User with email {email} not found', [
                    'email' => $this->ansiFormat($email, Console::BOLD)
                ]),
            ]
        ]);
    }

}
