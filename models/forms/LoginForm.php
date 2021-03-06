<?php

namespace inblank\activeuser\models\forms;

use inblank\activeuser\traits\CommonTrait;
use yii;
use yii\base\Model;
use yii\base\Security;

class LoginForm extends Model
{
    use CommonTrait;

    /**
     * @var string user email for login
     */
    public $email;
    /**
     * @var string user password for login in scenario SCENARIO_LOGIN
     */
    public $password;
    /**
     * @var bool remember me check in scenario SCENARIO_LOGIN
     */
    public $remember;

    /**
     * Founded user
     * @var \inblank\activeuser\models\User
     */
    protected $user = false;

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('activeuser_general', 'Email'),
            'password' => Yii::t('activeuser_general', 'Password'),
            'remember' => Yii::t('activeuser_general', 'Remember me next time'),
            'hash' => Yii::t('activeuser_general', 'Special hash'),
        ];
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            ['password', function () {
                if ($this->getUser() === null || !(new Security())->validatePassword($this->password, $this->getUser()->pass_hash)) {
                    $this->addError('password', Yii::t('activeuser_general', 'Invalid email or password'));
                }
            }],
            ['email', function () {
                if ($this->getUser() !== null) {
                    if (!$this->getUser()->isConfirmed()) {
                        $this->addError('password', Yii::t('activeuser_general', 'You need to confirm your email address'));
                    } elseif ($this->getUser()->isBlocked()) {
                        $this->addError('password', Yii::t('activeuser_general', 'Your account has been blocked'));
                    }
                }
            }],
        ];
    }

    /**
     * Validates form and logs the user in.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->getUser()->login($this->getUser(), $this->remember ? $this->module->rememberTime : 0);
        }
        return false;
    }

    protected function getUser()
    {
        if ($this->user === false) {
            $userClass = self::di('User');
            $this->user = empty($this->email) ? null : $userClass::findOne(['email' => $this->email]);
        }
        return $this->user;
    }
}
