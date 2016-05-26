<?php

namespace inblank\activeuser\models\forms;

use inblank\activeuser\models\User;
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
     * @var User
     */
    protected $user;

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
                if (empty($this->user) || !(new Security())->validatePassword($this->password, $this->user->pass_hash)) {
                    $this->addError('password', Yii::t('activeuser_general', 'Invalid email or password'));
                }
            }],
            ['email', function () {
                if (!empty($this->user)) {
                    if (!$this->user->isConfirmed()) {
                        $this->addError('password', Yii::t('activeuser_general', 'You need to confirm your email address'));
                    } elseif ($this->user->isBlocked()) {
                        $this->addError('password', Yii::t('activeuser_general', 'Your account has been blocked'));
                    }
                }
            }],
        ];
    }

    /** @inheritdoc */
    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }
        $this->user = empty($this->email) ? null : User::findOne(['email' => $this->email]);
        return true;
    }

    /**
     * Validates form and logs the user in.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->getUser()->login($this->user, $this->remember ? $this->module->rememberTime : 0);
        }
        return false;
    }
}
