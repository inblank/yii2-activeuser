<?php
/**
 * Bootstrap script for the module yii2-activeuser
 *
 * @link https://github.com/inblank/yii2-activeuser
 * @copyright Copyright (c) 2016 Pavel Aleksandrov <inblank@yandex.ru>
 * @license http://opensource.org/licenses/MIT
 */
namespace inblank\activeuser;

use inblank\activeuser\traits\CommonTrait;
use yii;
use yii\base\Application;
use yii\console\Application as ConsoleApplication;
use yii\i18n\PhpMessageSource;
use yii\web\GroupUrlRule;

/**
 * Bootstrap class for the module yii2-activeuser
 * @package inblank\activeuser
 */
class Bootstrap implements yii\base\BootstrapInterface
{
    use CommonTrait;

    /** @var array Model's map */
    private $_modelMap = [
    ];

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        /** @var Module $module */
        /** @var \yii\db\ActiveRecord $modelName */
        if ($app->hasModule('activeuser') && ($module = $app->getModule('activeuser')) instanceof Module) {
            $this->_modelMap = array_merge($this->_modelMap, $module->modelMap);
            foreach ($this->_modelMap as $name => $definition) {
                $class = "inblank\\activeuser\\models\\" . $name;
                Yii::$container->set($class, $definition);
                $modelName = is_array($definition) ? $definition['class'] : $definition;
                $module->modelMap[$name] = $modelName;
            }
            if ($app instanceof ConsoleApplication) {
                $app->controllerMap['activeuser'] = [
                    'class' => 'inblank\activeuser\commands\DefaultController',
                ];
            } else {
                // init user
                Yii::$container->set('yii\web\User', [
                    'loginUrl' => ['/activeuser/account/login'],
                    'identityClass' => self::di('User'),
                ]);
                $configUrlRule = [
                    'prefix' => $module->urlPrefix,
                    'rules' => defined('IS_BACKEND') ? $module->urlRulesBackend : $module->urlRulesFrontend,
                ];
                if ($module->urlPrefix != 'activeuser') {
                    $configUrlRule['routePrefix'] = 'activeuser';
                }
                $app->urlManager->addRules([new GroupUrlRule($configUrlRule)], false);
                if (defined('IS_BACKEND')) {
                    // is backend, and controller have other namespace
                    $module->controllerNamespace = 'inblank\activeuser\controllers\backend';
                    $module->frontendUrlManager = new yii\web\UrlManager([
                        'baseUrl' => '/',
                        'enablePrettyUrl' => true,
                        'showScriptName' => false,
                    ]);
                    $configUrlRule['rules'] = $module->urlRulesFrontend;
                    $module->frontendUrlManager->addRules([new GroupUrlRule($configUrlRule)], false);
                }
            }
            if (!isset($app->get('i18n')->translations['activeuser*'])) {
                $app->get('i18n')->translations['activeuser*'] = [
                    'class' => PhpMessageSource::className(),
                    'basePath' => __DIR__ . '/messages',
                ];
            }
        }
    }
}
