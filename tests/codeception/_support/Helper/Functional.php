<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use app\components\ApplicationMock;

class Functional extends \Codeception\Module
{
    /**
     * Testing module id
     * @var string
     */
    protected $_moduleId = 'activeuser';

    /**
     * Change currently running application the module params
     * @param array $params new params value
     * @param string|null $moduleId module id for change configuration. if null, use Fnctional::$_moduleId
     */
    public function changeModuleParams($params, $moduleId = null)
    {
        ApplicationMock::changeModule($moduleId ?: $this->_moduleId, $params);
    }
}
