<?php

namespace inblank\activeuser\models\forms;

use inblank\activeuser\models\User;
use inblank\activeuser\traits\CommonTrait;
use yii;
use yii\base\Model;
use yii\base\Security;

class ChangePasswordForm extends Model
{
    use CommonTrait;

    /**
     * @var string old user password
     */
    public $oldPassword;

    /**
     * @var string new user password
     */
    public $newPassword;

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'oldPassword' => Yii::t('activeuser_frontend', 'Old Password'),
            'newPassword' => Yii::t('activeuser_frontend', 'New Password'),
        ];
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['oldPassword', 'newPassword',], 'required'],
            ['newPassword', function () {
                if (!(new Security())->validatePassword($this->oldPassword, Yii::$app->getUser()->identity->pass_hash)) {
                    $this->addError('oldPassword', Yii::t('activeuser_frontend', 'Invalid old password'));
                }
            }],
        ];
    }

    /**
     * Validates form and logs the user in.
     * @return bool whether the user is logged in successfully
     */
    public function changePassword()
    {
        if (!$this->validate()) {
            return false;
        }
        /** @var User $user */
        $user = Yii::$app->getUser()->identity;
        $user->password = $this->newPassword;
        $user->newPassword(false);
        return true;
    }

    /**
     * Reset from fields
     */
    public function reset()
    {
        $this->oldPassword = $this->newPassword = null;
    }
}
