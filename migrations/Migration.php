<?php

namespace inblank\activeuser\migrations;

use yii;
use yii\helpers\Console;

class Migration extends \yii\db\Migration
{
    const TAB_USERS = 'users';
    const TAB_PROFILES = 'profiles';

    /**
     * @var string
     */
    protected $tableOptions;

    protected $tableGroup = 'activeuser_';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        switch (Yii::$app->db->driverName) {
            case 'mysql':
                $this->tableOptions = "ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 DEFAULT COLLATE=utf8_general_ci";
                break;
            case 'pgsql':
                $this->tableOptions = null;
                break;
            default:
                throw new \RuntimeException('Your database is not supported!');
        }
    }

    protected function stderr($string)
    {
        if (Console::streamSupportsAnsiColors(\STDOUT)) {
            $string = Console::ansiFormat("    Error: " . $string, [Console::FG_RED]);
        }
        return fwrite(\STDERR, $string);
    }

    /**
     * Real table name builder
     * @param string $name table name
     * @return string
     */
    protected function tn($name)
    {
        return '{{%' . $this->tableGroup . $name . '}}';
    }

    /**
     * Foreign key relation names generator
     * @param string $table1 first table in relation
     * @param string $table2 second table in relation
     * @return string
     */
    protected function fk($table1, $table2)
    {
        return 'fk__' . $this->tableGroup . $table1 . '__' . $table2;
    }

    /**
     * Primary key names generator
     * @param string $table table name
     * @return string
     */
    protected function pk($table)
    {
        return 'pk_' . $this->tableGroup . $table;
    }

}
