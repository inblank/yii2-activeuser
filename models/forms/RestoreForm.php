<?php

namespace inblank\activeuser\models\forms;

use inblank\activeuser\models\User;
use inblank\activeuser\traits\CommonTrait;
use yii;
use yii\base\Model;

class RestoreForm extends Model
{
    use CommonTrait;

    const SCENARIO_PASSWORD = 'password';
    const SCENARIO_EMAIL = 'email';

    /**
     * @var string user email for restore password
     */
    public $email;
    /**
     * @var string new password
     */
    public $password;

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('activeuser_general', 'Email'),
            'password' => Yii::t('activeuser_general', 'Password'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'required', 'on' => self::SCENARIO_EMAIL],
            ['email', 'email', 'skipOnEmpty' => true],

            ['password', 'required', 'on' => self::SCENARIO_PASSWORD],
            ['password', 'string', 'length' => [6, 20], 'skipOnEmpty' => true],
        ];
    }

    /**
     * Restore
     * @return bool
     * @throws yii\base\InvalidConfigException
     */
    public function restore()
    {
        if (!$this->validate()) {
            return false;
        }
        /** @var User $user */
        $user = Yii::createObject(User::className())->findOne(['email' => $this->email]);
        if ($user) {
            $user->restore();
        }
        return true;
    }
    /**
     * Change password
     * @return bool
     * @throws yii\base\InvalidConfigException
     */
    public function changePassword()
    {
        if (!$this->validate()) {
            return false;
        }
        /** @var User $user */
        $user = Yii::createObject(User::className())->findOne(['email' => $this->email]);
        if ($user) {
            $user->password = $this->password;
            $user->newPassword();
        }
        return true;
    }
}
