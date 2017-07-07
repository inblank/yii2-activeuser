<?php

use inblank\activeuser\migrations\Migration;
use yii\db\Schema;

class m170707_070437_avatar_image extends Migration
{
    public function safeUp()
    {
        // set default value for `avatar` field to NULL
        $tab = self::tn(self::TAB_USERS);
        $this->alterColumn($tab, 'avatar', Schema::TYPE_STRING . "(45) DEFAULT NULL");
        // replace all empty `avatar`
        $this->db->createCommand("update " . $tab . " set [[avatar]]=NULL where [[avatar]]=''")->execute();
    }

    public function safeDown()
    {
        $tab = self::tn(self::TAB_USERS);
        $this->db->createCommand("update " . $tab . " set [[avatar]]='' where [[avatar]] IS NULL")->execute();
        $this->alterColumn($tab, 'avatar', Schema::TYPE_STRING . "(45) NOT NULL DEFAULT ''");
    }
}
