<?php


use inblank\activeuser\migrations\Migration;
use yii\db\Schema;

class m160523_164512_activeuser_init extends Migration
{
    public function up()
    {
        // Accounts
        $tab = self::tn(self::TAB_USERS);
        $this->createTable($tab, [
            'id' => Schema::TYPE_PK,
            'status' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',

            'email' => Schema::TYPE_STRING . "(200) NOT NULL DEFAULT ''",
            'pass_hash' => Schema::TYPE_STRING . "(60) NOT NULL DEFAULT ''",

            'name' => Schema::TYPE_STRING . "(200) NOT NULL DEFAULT ''",
            'gender' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'birth' => Schema::TYPE_DATE . " DEFAULT NULL",
            'avatar' => Schema::TYPE_STRING . "(45) NOT NULL DEFAULT ''",

            'access_token' => Schema::TYPE_STRING . "(40) DEFAULT NULL",
            'auth_key' => Schema::TYPE_STRING . "(40) DEFAULT NULL",

            'token' => Schema::TYPE_STRING . "(40) DEFAULT NULL",
            'token_created_at' => Schema::TYPE_INTEGER . " NOT NULL DEFAULT 0",

            'registered_at' => Schema::TYPE_DATETIME . ' DEFAULT NULL',
        ], $this->tableOptions);
        $this->createIndex('unique_email', $tab, 'email', true);
        $this->createIndex('unique_access_token', $tab, 'access_token', true);
        $this->createIndex('unique_auth_key', $tab, 'auth_key', true);
        $this->createIndex('unique_token', $tab, 'token', true);

        // Accounts profiles
        $tab = self::tn(self::TAB_PROFILES);
        $this->createTable($tab, [
            'user_id' => Schema::TYPE_PK,
            'site' => Schema::TYPE_STRING . "(255) NOT NULL DEFAULT ''",
            'location' => Schema::TYPE_STRING . "(255) NOT NULL DEFAULT ''",
        ], $this->tableOptions);
        $this->addForeignKey(
            self::fk(self::TAB_PROFILES, self::TAB_USERS),
            $tab, 'user_id',
            self::tn(self::TAB_USERS), 'id',
            'CASCADE', 'RESTRICT'
        );
    }

    public function down()
    {
        $tables = [
            self::TAB_PROFILES,
            self::TAB_USERS,
        ];
        foreach ($tables as $table) {
            $this->dropTable(self::tn($table));
        }
        return true;
    }

}
