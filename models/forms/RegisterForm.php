<?php

namespace inblank\activeuser\models\forms;

use inblank\activeuser\models\User;
use inblank\activeuser\traits\CommonTrait;
use yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class RegisterForm extends Model
{
    use CommonTrait;

    const SCENARIO_EMAIL = 1;

    /**
     * @var string user email for login
     */
    public $email;
    /**
     * @var string user name
     */
    public $name;

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('activeuser_general', 'Email'),
            'name' => Yii::t('activeuser_general', 'Name'),
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
                self::SCENARIO_DEFAULT => ['email', 'name', 'password'],
                self::SCENARIO_EMAIL => ['email'],
            ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'name'], 'required', 'on'=>self::SCENARIO_DEFAULT],
            ['email', 'required', 'on'=>self::SCENARIO_EMAIL],
            ['email', 'unique'],
        ];
    }

    public function register(){
        if (!$this->validate()) {
            return false;
        }

        /** @var User $user */
        $user = Yii::createObject(User::className());
        $user->setAttributes($this->attributes());
        if (!$user->register()) {
            return false;
        }
        return true;
    }
}
