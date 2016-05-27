<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use app\components\ApplicationMock;
use inblank\activeuser\Module;
use yii;

class Functional extends \Codeception\Module
{
    /**
     * Reflected Yii2 module from codeception
     * @var Module
     */
    protected $_codeceptionModule;
    /**
     * Testing module name
     * @var string
     */
    protected $_moduleName = 'activeuser';

    /**
     * Change currently running application the module params
     * @param array $params new params value
     * @throws \Codeception\Exception\ModuleException
     */
    public function changeModuleParams($params){
        if($this->_codeceptionModule===null) {
            // on first call get codeception module reflection
            $cl = new \ReflectionClass($this->getModule('Yii2')->client);
            $prop = $cl->getProperty('app');
            $prop->setAccessible(true);
            $this->_codeceptionModule = $prop->getValue($this->getModule('Yii2')->client)->getModule($this->_moduleName);
        }
        // get module from current Yii application
        //$currentRunningModule = Yii::$app->getModule($this->_moduleName);
        foreach($params as $name=>$value) {
            if($this->_codeceptionModule->canSetProperty($name)) {
                $this->_codeceptionModule->$name = $value;
//                $currentRunningModule->$name = $value;
            }
        }
        // change currently running application and edit config for future use
        ApplicationMock::changeModule($this->_moduleName, $params);
    }
}
