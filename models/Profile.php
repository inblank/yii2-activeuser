<?php

namespace inblank\activeuser\models;

use yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%activeuser_profiles}}".
 * @property integer $user_id user identifier
 * @property string $site user web site
 * @property string $location user location
 */
class Profile extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%activeuser_profiles}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['user_id', 'required'],
            ['user_id', 'exist', 'targetClass' => User::className(), 'targetAttribute' => 'id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'ID',
            'site' => Yii::t('activeuser_general', 'Web site'),
            'location' => Yii::t('activeuser_general', 'Location'),
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
