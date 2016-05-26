<?php

namespace inblank\activeuser\models\forms;

use inblank\activeuser\models\User;
use inblank\activeuser\traits\CommonTrait;
use yii;
use yii\base\Model;
use yii\base\Security;
use yii\helpers\ArrayHelper;

class LoginForm extends Model
{
    use CommonTrait;

    /**
     * Scenario for login use only email
     */
    const SCENARIO_EMAIL_LOGIN = 1;

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
     * Hash for login by email
     * @var string
     */
    public $hash;

    /**
     * Founded user
     * @var User
     */
    protected $user;

    public function init()
    {
        parent::init();
        if ($this->module->loginByUniqueURL) {
            $this->setScenario(self::SCENARIO_EMAIL_LOGIN);
        }
    }

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

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return ArrayHelper::merge(
            parent::scenarios(),
            [
                self::SCENARIO_DEFAULT => ['email', 'password', 'remember'],
                self::SCENARIO_EMAIL_LOGIN => ['email'],
            ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'required'],
            ['password', 'required'],

            // check password
            ['password', function () {
                if (empty($this->user) || !(new Security())->validatePassword($this->password, $this->user->pass_hash)) {
                    $this->addError('password', Yii::t('activeuser_general', 'Invalid email or password'));
                }
            }],

            // check user status
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
            if (!$this->scenario === self::SCENARIO_EMAIL_LOGIN) {
                if (!empty($this->user->name)) {
                    $to = [$this->user->email => $this->user->name];
                } else {
                    $to = $this->user->email;
                }
                $mailer = Yii::$app->mailer;
                $mailer->viewPath = '@inblank\activeuser\views\mails';
                $message = $mailer
                    ->compose('login', [
                        'user' => $this->user,
                        'url' => $this->module->loginUrl,
                        'hash' => $this->user->generateToken(),
                    ])
                    ->setTo($to)
                    ->setSubject(Yii::t('activeuser_general', 'Your login URL to site'));
                return $mailer->send($message);
            } else {
                return Yii::$app->getUser()->login($this->user, $this->remember ? $this->module->rememberTime : 0);
            }
        }
        return false;
    }

}
