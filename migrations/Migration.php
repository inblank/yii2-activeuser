<?php

namespace inblank\activeuser\migrations;

use yii;
use yii\helpers\Console;

class Migration extends \yii\db\Migration
{
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
            case 'pgsql':
                $this->tableOptions = null;
                break;
            default:
                throw new \RuntimeException('Your database is not supported!');
        }
    }

    /**
     * Get full table name for use in migration scripts
     * @param string $tableName table name without prefixes and tablegroup
     * @return string
     */
    public function tab($tableName)
    {
        return '{{%' . $this->tableGroup . $tableName . '}}';
    }

    protected function stderr($string)
    {
        if (Console::streamSupportsAnsiColors(\STDOUT)) {
            $string = Console::ansiFormat("    Error: " . $string, [Console::FG_RED]);
        }
        return fwrite(\STDERR, $string);
    }

}
