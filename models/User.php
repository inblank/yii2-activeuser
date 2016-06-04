<?php
/**
 * User model for the module yii2-activeuser
 *
 * @link https://github.com/inblank/yii2-activeuser
 * @copyright Copyright (c) 2016 Pavel Aleksandrov <inblank@yandex.ru>
 * @license http://opensource.org/licenses/MIT
 */
namespace inblank\activeuser\models;

use inblank\activeuser\traits\CommonTrait;
use yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%activeuser_users}}"
 *
 * Table fields:
 * @property integer $id user identifier
 * @property integer $status user status. STATUS_ACTIVE, STATUS_BLOCKED, STATUS_CONFIRM, STATUS_RESTORE
 * @property string $email user email used as login
 * @property string $pass_hash hash of user password
 * @property string $name real user name
 * @property int $gender user gender. User::MALE, User::FEMALE. If empty, not set
 * @property string $birth user birth date
 * @property string $avatar user avatar filename
 * @property string $access_token user access token for use site API
 * @property string $auth_key user key for access by `remember me`
 * @property string $token token for registration confirm or restore password
 * @property int $token_created_at the time when the token was created
 * @property string $registered_at user registration date
 *
 * Relations:
 * @property Profile $profile user profile data
 */
class User extends ActiveRecord implements yii\web\IdentityInterface
{
    use CommonTrait;

    /** Male gender */
    const MALE = 1;
    /** Female gender */
    const FEMALE = 2;

    /** Active user status */
    const STATUS_ACTIVE = 1;
    /** Blocked user status */
    const STATUS_BLOCKED = 2;
    /** User await email confirmation status */
    const STATUS_CONFIRM = 3;
    /** User await access restore status */
    const STATUS_RESTORE = 4;

    // TODO where store password between data enter and confirmation email? Password must be send with congratulation email
    /** @var string password field on registration on restore */
    public $password;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%activeuser_users}}';
    }

    /**
     * @inheritdoc
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * Finds an identity by the given ID.
     * @param string|integer $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Find user by token
     * @param string $token for search
     * @return null|static
     */
    public static function findByToken($token)
    {
        return empty($token) ? null : static::findOne(['token' => $token]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        if (empty($token)) {
            return null;
        }
        return static::findOne(['access_token' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Confirm user registration
     * @return bool return false if cannot confirm.
     * If in errors list has key `error`, that user already confirmed
     * If in errors list has key `token`, that confirm token was expired
     */
    public function confirm()
    {
        if ($this->isConfirmed()) {
            $this->addError('error', Yii::t('activeuser_general', 'User already confirmed'));
            return false;
        }
        if ($this->isTokenExpired($this->module->confirmationTime)) {
            $this->addError('token', Yii::t('activeuser_general', 'You token was expired'));
            return false;
        }
        $this->updateAttributes([
            'token' => '',
            'token_created_at' => 0,
            'status' => self::STATUS_ACTIVE,
        ]);
        $this->module->sendMessage('register', [
            'user' => $this,
        ]);
        return true;
    }

    /**
     * Check that user was confirmed
     * @return bool
     */
    public function isConfirmed()
    {
        return $this->status !== self::STATUS_CONFIRM;
    }

    /**
     * @param int $timeToExpire checks that the token was expired
     * @return bool
     */
    public function isTokenExpired($timeToExpire)
    {
        return $this->token_created_at + $timeToExpire < time();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        // todo check password length
        return [
            [['email', 'password'], 'required'],
            ['email', 'unique'],
            ['email', 'string', 'max' => 200],
            ['email', 'email'],
            ['name', 'string', 'max' => 200],
            ['name', function () {
                if (in_array('name', $this->module->registrationFields) && empty($this->name)) {
                    $this->addError('name', Yii::t('activeuser_general', 'Name cannot be blank.'));
                }
            }, 'skipOnEmpty' => false],
            ['status', 'in', 'range' => [
                self::STATUS_ACTIVE,
                self::STATUS_BLOCKED,
                self::STATUS_CONFIRM,
                self::STATUS_RESTORE,
            ]],
            ['status', 'default', 'value' => function () {
                return $this->module->enableConfirmation ? self::STATUS_CONFIRM : self::STATUS_ACTIVE;
            }],
            ['gender', 'in', 'range' => [
                0,
                self::MALE,
                self::FEMALE,
            ]],
            ['birth', 'date', 'format' => 'php:Y-m-d', 'skipOnEmpty' => true,],
            ['token_created_at', 'integer'],
            ['registered_at', 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['registered_at', 'default', 'value' => function () {
                return date('Y-m-d H:i:s');
            }],
            // rules below for prevent nullable value of attributes
            [['name', 'avatar'], 'default', 'value' => ''],
            [['gender', 'token_created_at'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => Yii::t('activeuser_general', 'Status'),
            'email' => Yii::t('activeuser_general', 'Email'),
            'password' => Yii::t('activeuser_general', 'Password'),
            'pass_hash' => Yii::t('activeuser_general', 'Password hash'),
            'name' => Yii::t('activeuser_general', 'Name'),
            'gender' => Yii::t('activeuser_general', 'Gender'),
            'birth' => Yii::t('activeuser_general', 'Birth'),
            'avatar' => Yii::t('activeuser_general', 'Avatar'),
            'access_token' => Yii::t('activeuser_general', 'Access token'),
            'auth_key' => Yii::t('activeuser_general', 'Auth key'),
            'token' => Yii::t('activeuser_general', 'Token'),
            'token_created_at' => Yii::t('activeuser_general', 'Token created'),
            'registered_at' => Yii::t('activeuser_general', 'Registered'),
        ];
    }

    /**
     * Get user profile
     * @return yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($this->getIsNewRecord() && !empty($this->password)) {
            $this->pass_hash = Yii::$app->getSecurity()->generatePasswordHash($this->password);
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            // add profile for new user
            Yii::createObject([
                'class' => Profile::className(),
                'user_id' => $this->id,
            ])->save();
        }
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_INSERT,
        ];
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|integer an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return boolean whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        $userAuthKey = $this->getAuthKey();
        return $this->status === self::STATUS_ACTIVE && !empty($userAuthKey) && $userAuthKey === $authKey;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Check active user
     * @return bool
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * User creation
     * For create the user you always must set attributes `email`, `password` and `name`
     * @param bool $sendEmail whether to send email about registration
     * @return bool
     */
    public function create($sendEmail = false)
    {
        $oldRegisterFields = $this->module->registrationFields;
        $this->module->registrationFields = ['password', 'name'];
        if ($this->getIsNewRecord() == false) {
            throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }
        if (empty($this->password)) {
            // password autogenerate
            $this->password = $this->generatePassword();
        }
        $this->status = self::STATUS_ACTIVE;
        $isCreated = $this->save();
        $this->module->registrationFields = $oldRegisterFields;
        if (!$isCreated) {
            return false;
        }
        if ($sendEmail) {
            // send email with registration congratulation
            $this->module->sendMessage('register', [
                'user' => $this,
            ]);
        }
        return true;
    }

    /**
     * User registration
     * @return bool
     */
    public function register()
    {
        if ($this->getIsNewRecord() == false) {
            throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }
        if (!$this->module->enableRegistration) {
            $this->addError('registration', Yii::t('activeuser_general', 'Registration is not available'));
            return false;
        }
        if (!in_array('password', $this->module->registrationFields)) {
            // password autogenerate
            $this->password = $this->generatePassword();
        }
        if (!$this->save()) {
            return false;
        }
        if ($this->module->enableConfirmation) {
            $this->generateToken();
            // send email with confirm link
            $this->module->sendMessage('confirm', [
                'user' => $this,
            ]);
        } elseif ($this->module->enableRegistrationEmail) {
            // send email with registration congratulation
            $this->module->sendMessage('register', [
                'user' => $this,
            ]);
        }
        return true;
    }

    /**
     * Password generator
     * @return mixed
     */
    public function generatePassword()
    {
        return (new \PWGen())->generate();
    }

    /**
     * Generate special hash
     * @return string
     */
    public function generateToken()
    {
        $this->updateAttributes([
            'token' => Yii::$app->security->generateRandomString(40),
            'token_created_at' => time(),
        ]);
        return $this->token;
    }

    /**
     * Start password restore procedure
     * @return bool
     */
    public function restore()
    {
        if (!$this->module->enablePasswordRestore) {
            $this->addError('error', Yii::t('activeuser_general', 'Password restore by email is disabled'));
            return false;
        }
        if ($this->isBlocked() || !$this->isConfirmed()) {
            $this->addError('error', Yii::t('activeuser_general', 'You cannot start restore procedure'));
            return false;
        }
        $this->generateToken();
        $this->updateAttributes([
            'status' => self::STATUS_RESTORE,
        ]);
        $this->module->sendMessage('restore', [
            'user' => $this,
        ]);
        return true;
    }

    /**
     * Check blocked user
     * @return bool
     */
    public function isBlocked()
    {
        return $this->status === self::STATUS_BLOCKED;
    }

    /**
     * Block user
     * @param bool $sendMail whether to send confirmation email about blocking.
     * If null, use global setting Module::$enableBlockingEmail
     * @return bool return false if user already blocked or not confirmed
     */
    public function block($sendMail = null)
    {
        if ($this->isBlocked() || !$this->isConfirmed()) {
            return false;
        }
        if ($sendMail === null) {
            $sendMail = $this->module->enableBlockingEmail;
        }
        $this->updateAttributes([
            'status' => self::STATUS_BLOCKED
        ]);
        if ($sendMail) {
            $this->module->sendMessage('block', [
                'user' => $this,
            ]);
        }
        return true;
    }

    /**
     * Unblock user
     * @param bool $sendMail whether to send confirmation email about unblocking.
     * If null, use global setting Module::$enableUnblockingEmail
     * @return bool return false if user not blocked
     */
    public function unblock($sendMail = null)
    {
        if (!$this->isBlocked()) {
            return false;
        }
        if ($sendMail === null) {
            $sendMail = $this->module->enableBlockingEmail;
        }
        $this->updateAttributes([
            'status' => self::STATUS_ACTIVE
        ]);
        if ($sendMail) {
            $this->module->sendMessage('unblock', [
                'user' => $this,
            ]);
        }
        return true;
    }

    /**
     * Change the user password
     * @return bool
     */
    public function changePassword()
    {
        if (!$this->isRestore()) {
            $this->addError('error', Yii::t('activeuser_general', 'User not request restore procedure'));
            return false;
        }
        if ($this->isTokenExpired($this->module->recoveryTime)) {
            $this->addError('token', Yii::t('activeuser_general', 'You token was expired'));
            return false;
        }
        if (empty($this->password)) {
            if (!$this->module->generatePassOnRestore) {
                $this->addError('password', Yii::t('activeuser_general', 'Password cannot be blank'));
                return false;
            }
            $this->password = $this->generatePassword();
        }
        $this->updateAttributes([
            'pass_hash' => Yii::$app->getSecurity()->generatePasswordHash($this->password),
            'token' => '',
            'token_created_at' => 0,
            'status' => self::STATUS_ACTIVE,
        ]);
        $this->module->sendMessage('passchanged', [
            'user' => $this,
        ]);
        return true;
    }

    /**
     * Check that user request restore
     * @return bool
     */
    public function isRestore()
    {
        return $this->status === self::STATUS_RESTORE;
    }

}
