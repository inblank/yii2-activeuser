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
trait CommonTrait{
    /**
     * Module instance
     * @var Module
     */
    protected $_module;

    /**
     * Get module instance
     * @return Module
     * @throws InvalidConfigException
     */
    public function getModule(){
        if($this->_module===null) {
            if (!Yii::$app->hasModule('activeuser')) {
                throw new InvalidConfigException('You must configure module as `activeuser`');
            }
            $this->_module = Yii::$app->getModule('activeuser');
        }
        return $this->_module;
    }

}
