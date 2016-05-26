<?php

namespace inblank\activeuser\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[User]].
 *
 * @see User
 */
class UserQuery extends ActiveQuery
{
    /**
     * Filter only active user
     * @return $this
     */
    public function active()
    {
        $this->andWhere('[[status]]=' . User::STATUS_ACTIVE);
        return $this;
    }

    /**
     * Filter only blocked user
     * @return $this
     */
    public function blocked()
    {
        $this->andWhere('[[status]]=' . User::STATUS_BLOCKED);
        return $this;
    }

    /**
     * Filter only user waiting confirmation
     * @return $this
     */
    public function notConfirmed()
    {
        $this->andWhere('[[status]]=' . User::STATUS_CONFIRM);
        return $this;
    }

    /**
     * Filter user that requested access restore
     * @return $this
     */
    public function restoring()
    {
        $this->andWhere('[[status]]=' . User::STATUS_RESTORE);
        return $this;
    }

    /**
     * @inheritdoc
     * @return User[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return User|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
