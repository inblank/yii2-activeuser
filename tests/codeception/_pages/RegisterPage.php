<?php

namespace tests\codeception\_pages;

use inblank\activeuser\models\forms\RegisterForm;
use yii;
use yii\codeception\BasePage;

/**
 * Represents register page.
 *
 * @property \FunctionalTester $actor
 */
class RegisterPage extends BasePage
{
    /** @inheritdoc */
    public $route = '/activeuser/account/register';

    /**
     * Register
     * @param array $params
     */
    public function register($params=[])
    {
        foreach(['email','name','password'] as $fieldName) {
            if (array_key_exists($fieldName, $params)) {
                $this->actor->fillField($this->fullFieldId($fieldName), $params[$fieldName]);
            }
        }
        $this->actor->click('Register');
    }

    public function fullFieldId($name)
    {
        return '#'.strtolower(Yii::createObject(RegisterForm::className())->formName().'-'.$name);
    }

    public function getUrlFromEmail($linkType, $messageText)
    {
        preg_match_all("/href.+\"(.*)\">/imsU", $messageText, $m);
        foreach($m[1] as $url) {
            $url = htmlspecialchars_decode(rawurldecode(quoted_printable_decode($url)));
            parse_str(parse_url($url, PHP_URL_QUERY), $url);
            if(!empty($url['r']) && strpos($url['r'], $linkType)!==false){
                return $url;
            }
        }
        return false;
    }
}
