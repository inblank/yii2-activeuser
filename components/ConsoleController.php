<?php
/**
 * Console controller
 *
 * @link https://github.com/inblank/yii2-activeuser
 * @copyright Copyright (c) 2016 Pavel Aleksandrov <inblank@yandex.ru>
 * @license http://opensource.org/licenses/MIT
 */

namespace inblank\activeuser\components;

use yii;
use yii\helpers\Console;

/**
 * Class ConsoleController
 *
 * @package inblank\activeuser\traits
 */
class ConsoleController extends yii\console\Controller
{
    /**
     * Confirm action
     * @param string $message message for confirm
     * @param bool $default default selection
     * @throws yii\base\ExitException
     */
    public function confirmAction($message, $default = false)
    {
        if (!Console::confirm($message, $default)) {
            Console::error(Yii::t('activeuser_backend', 'Action canceled'));
            Yii::$app->end();
        }
    }

    /**
     * Show action usage
     * @param string $required required params separated by comma
     * @param string $optional optional params separated by comma
     * @param array $errors errors to output
     * @throws yii\base\ExitException
     */
    protected function showUsage($required = '', $optional = '', $errors = [])
    {
        $description = Yii::t('activeuser_backend', $this->getActionHelp($this->action));
        Console::output($description);
        Console::output(str_pad('', mb_strlen($description), '-'));
        $paramsString = [];
        if (!empty($required)) {
            foreach (explode(',', $required) as $param) {
                $paramsString[] = '<' . trim($param) . '>';
            }
        }
        if (!empty($optional)) {
            foreach (explode(',', $optional) as $param) {
                $paramsString[] = '[' . trim($param) . ']';
            }
        }
        Console::output(
            Yii::t('activeuser_backend', 'Usage') . ': ' .
            Console::ansiFormat(
                'yii ' . $this->id . "/" . $this->action->id . ' ' .
                implode(' ', $paramsString),
                [Console::BOLD]
            )
        );
        Console::output();
        if (!empty($errors)) {
            $this->showErrors($errors);
        }
        yii::$app->end();
    }

    /**
     * Show errors
     * @param array $errors array of errors string
     * @throws yii\base\ExitException
     */
    protected function showErrors($errors)
    {
        foreach ((array)$errors as $err) {
            Console::error(Console::ansiFormat(Yii::t('activeuser_backend', "Error") . ": ", [Console::FG_RED]) . $err[0]);
        }
        yii::$app->end();
    }

}
