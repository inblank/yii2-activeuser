<?php

namespace inblank\activeuser\traits;

use inblank\activeuser\Module;
use yii;
use yii\base\InvalidConfigException;

/**
 * Class CommonTrait
 * @property Module $module current module
 *
 * @package inblank\activeuser\traits
 */
trait CommonTrait
{
    /**
     * Get module
     * @return \inblank\activeuser\Module
     * @throws InvalidConfigException
     */
    static function getModule()
    {
        /** @var \inblank\activeuser\Module $module */
        if ($module = Yii::$app->getModule('activeuser')) {
            return $module;
        }
        throw new InvalidConfigException('You must configure module as `activeuser`');
    }

    /**
     * Models dependency injection resolver
     * @param string $name class name for resolve
     * @return mixed
     * @throws InvalidConfigException
     */
    public static function di($name)
    {
        $class = 'inblank\activeuser\models\\' . $name;
        return empty(self::getModule()->modelMap[$name]) ? $class : self::getModule()->modelMap[$name];
    }

    /**
     * Models table name with dependency injection resolver
     * @param string $name class name for get table name
     * @return string
     * @throws InvalidConfigException
     */
    public static function tn($name)
    {
        $name = self::di($name);
        return $name::tableName();
    }
}
