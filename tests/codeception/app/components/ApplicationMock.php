<?php
namespace app\components;

use Yii;
use yii\web\Application;

/**
 * Yii2 application mock class
 * @package app\components
 */
class ApplicationMock extends Application
{
    /** @var array currently used application configuration */
    public static $config;

    /** @inheritdoc */
    public function __construct($config = [])
    {
        if (self::$config === null) {
            // if first call, store original configuration
            self::$config = $config;
        }
        parent::__construct(self::$config);
    }

    /**
     * Change module configuration
     * @param string $moduleId module id
     * @param array $params new params
     * @param bool $merge whether to merge new and old parameters
     */
    public static function changeModule($moduleId, $params = [], $merge = true)
    {
        if (empty(self::$config['modules'])) {
            // create module field
            self::$config['modules'] = [];
        }
        if (!empty(self::$config['modules'][$moduleId]) && $merge) {
            // if we need merge configuration data
            $currentParams = self::$config['modules'][$moduleId];
            if (is_string($currentParams)) {
                $currentParams = [
                    'class' => $currentParams,
                ];
            }
            foreach ($params as $name => $value) {
                $currentParams[$name] = $value;
            }
            $params = $currentParams;
        }
        self::$config['modules'][$moduleId] = $params;
        // apply for current $app instance
        $module = Yii::$app->getModule($moduleId);
        if (!$merge || $module === null || (!empty($params['class']) && $module::className() != $params['class'])) {
            // new or changed
            Yii::$app->setModule($moduleId, $params);
        } else {
            // change the attributes
            foreach ($params as $name => $value) {
                if ($name !== 'class' && $module->canSetProperty($name)) {
                    $module->$name = $value;
                }
            }
        }
    }
}
