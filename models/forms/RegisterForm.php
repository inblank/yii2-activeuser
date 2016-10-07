<?php

namespace inblank\activeuser\models\forms;

use inblank\activeuser\traits\CommonTrait;
use yii;
use yii\base\Model;

class RegisterForm extends Model
{
    use CommonTrait;

    /**
     * @var string user email for register
     */
    public $email;
    /**
     * @var string user name for register
     */
    public $name;
    /**
     * @var string user password for register
     */
    public $password;

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('activeuser_general', 'Email'),
            'name' => Yii::t('activeuser_general', 'Name'),
            'password' => Yii::t('activeuser_general', 'Password'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => self::di('User'), 'targetAttribute' => 'email'],
            ['name', 'required', 'when' => function () {
                return $this->getModule()->isFieldForRegister('name');
            }],
            ['password', 'required', 'when' => function () {
                return $this->getModule()->isFieldForRegister('password');
            }],
            ['password', 'string', 'length' => [6, 20], 'skipOnEmpty' => true],
        ];
    }

    /**
     * Registration
     * @return bool
     * @throws yii\base\InvalidConfigException
     */
    public function register()
    {
        if (!$this->validate()) {
            return false;
        }
        /** @var \inblank\activeuser\models\User $user */
        $user = Yii::createObject(self::di('User'));
        $user->setAttributes($this->getAttributes());
        return $user->register();
    }
}
