<?php

namespace inblank\activeuser\models\forms;

use inblank\activeuser\models\User;
use inblank\activeuser\traits\CommonTrait;
use yii;
use yii\base\Model;

class ResendForm extends Model
{
    use CommonTrait;

    /**
     * @var string user email for register
     */
    public $email;

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('activeuser_general', 'Email'),
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
        ];
    }

    /**
     * Resend
     * @return bool
     * @throws yii\base\InvalidConfigException
     */
    public function resend()
    {
        if (!$this->validate()) {
            return false;
        }
        /** @var User $user */
        $user = Yii::createObject(User::className())->findOne(['email' => $this->email]);
        if ($user) {
            $user->resend();
        }
        return true;
    }
}
