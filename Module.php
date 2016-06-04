<?php
/**
 * Module yii2-activeuser
 *
 * @link https://github.com/inblank/yii2-activeuser
 * @copyright Copyright (c) 2016 Pavel Aleksandrov <inblank@yandex.ru>
 * @license http://opensource.org/licenses/MIT
 */
namespace inblank\activeuser;

use yii;
use yii\base\Module as BaseModule;

/**
 * This is the main module class
 *
 * @property array $modelMap
 *
 * @author Pavel Aleksandrov <inblank@yandex.ru>
 */
class Module extends BaseModule
{
    /** Module version */
    const VERSION = '0.1.0';

    /** @var array view templates for emails composing */
    public $mailViews = [
        'confirm' => 'confirm',
        'register' => 'register',
        'restore' => 'restore',
        'passchanged' => 'passchanged',
        'block' => 'block',
        'unblock' => 'unblock',
    ];
    /** @var bool whether to enable user registration */
    public $enableRegistration = true;
    /**
     * List of fields used for registration.
     * Email is always used and can be omitted.
     * If you not specify password, they will be generated automatically
     * You can specify: password, name, gender, birth
     * if you specify `password` or `name` they required for fill.
     * `gender` and `birth` is always optional.
     *
     * @var string[]
     */
    public $registrationFields = [];
    /** @var bool whether to enable send the email to the user for confirm the email address */
    public $enableConfirmation = true;
    /** @var bool whether to enable send notification email about register to the user */
    public $enableRegistrationEmail = true;
    /** @var bool whether to enable send notification email about user blocking */
    public $enableBlockingEmail = true;
    /** @var bool whether to enable send notification email about user unblocking */
    public $enableUnblockingEmail = true;
    /** @var bool whether to enable password restore by email */
    public $enablePasswordRestore = true;
    /**
     * @var bool whether to automatically generate password on restore
     * Password will be generated only if user password is empty
     */
    public $generatePassOnRestore = true;
    /**
     * Email sender address
     * If not set use Yii::$app->params['adminEmail'], and if they empty use 'no-reply@'.$_SERVER['HTTP_HOST']
     * Can be set as array ['email'=>'name']
     * @var string|array
     */
    public $sender;
    /**
     * Use only email for login (medium.com style)
     * If true, user's email used for send unique URL link to enter on site
     * @var bool
     */
    public $loginByEmail = false;
    /** @var int the time you want the user will be remembered without asking for credentials */
    public $rememberTime = 2592000;
    /** @var int the time before a confirmation token becomes invalid */
    public $confirmationTime = 86400; // one month
    /** @var int the time before a recovery token becomes invalid */
    public $recoveryTime = 10800; // one day
    /** @var array Model map */
    public $modelMap = [];
    /**
     * @var string The prefix for user module URL.
     * @See [[GroupUrlRule::prefix]]
     */
    public $urlPrefix = 'activeuser';
    /** @var array The rules for frontend to be used in URL management. */
    public $urlRulesFrontend = [
    ];
    public $frontendUrlManager;
    /** @var array The rules for backend to be used in URL management. */
    public $urlRulesBackend = [
    ];
    /**
     * The URL for login in email only mode
     * @var string|array
     */
    public $loginUrl = ['/activeuser/account/login'];
    /**
     * @var yii\mail\BaseMailer
     */
    protected $mailer;

    /**
     * Send email
     * @param int $type email type
     * @param array $params email views params
     */
    public function sendMessage($type, $params)
    {
        if ($this->mailer === null) {
            /** @var yii\swiftmailer\Mailer mailer */
            $this->mailer = Yii::$app->mailer;
            $this->mailer->viewPath = $this->getViewPath() . '/mails';
            $this->mailer->getView()->theme = Yii::$app->view->theme;
        }
        switch ($type) {
            case 'register':
                if ($this->enableRegistrationEmail) {
                    $message = $this->mailer->compose($this->mailViews[$type], $params);
                    $message->setSubject(Yii::t('activeuser_general', 'Thank you for register on site'));
                }
                break;
            case 'confirm':
                if ($this->enableConfirmation) {
                    $message = $this->mailer->compose($this->mailViews[$type], $params);
                    $message->setSubject(Yii::t('activeuser_general', 'Email address confirmation needed'));
                }
                break;
            case 'restore':
                if ($this->enableConfirmation) {
                    $message = $this->mailer->compose($this->mailViews[$type], $params);
                    $message->setSubject(Yii::t('activeuser_general', 'Password restore request'));
                }
                break;
            case 'passchanged':
                if ($this->enableConfirmation) {
                    $message = $this->mailer->compose($this->mailViews[$type], $params);
                    $message->setSubject(Yii::t('activeuser_general', 'Password was changed'));
                }
                break;
            case 'block':
                $message = $this->mailer->compose($this->mailViews[$type], $params);
                $message->setSubject(Yii::t('activeuser_general', 'You are blocked'));
                break;
            case 'unblock':
                $message = $this->mailer->compose($this->mailViews[$type], $params);
                $message->setSubject(Yii::t('activeuser_general', 'You are unblocked'));
                break;
        }
        if (!empty($message)) {
            $user = $params['user'];
            if ($this->sender === null) {
                $this->sender = isset(Yii::$app->params['adminEmail']) ? Yii::$app->params['adminEmail'] : 'no-reply@' . (empty($_SERVER['HTTP_HOST']) ? 'example.com' : $_SERVER['HTTP_HOST']);
            }
            $message->setTo(empty($user->name) ? $user->email : [$user->email => $user->name]);
            $message->setFrom($this->sender);
            $this->mailer->send($message);
        }
    }

    /**
     * Check that registration enabled
     * @return bool
     */
    public function isRegistrationEnabled()
    {
        return $this->enableRegistration;
    }

    /**
     * @inheritdoc
     */
    public function getViewPath()
    {
        return defined('IS_BACKEND') ? $this->getBasePath() . DIRECTORY_SEPARATOR . 'views/_backend' : parent::getViewPath();
    }

    /**
     * Check that field need for register
     * @param string $name field name
     * @return bool
     */
    public function isFieldForRegister($name)
    {
        return in_array($name, $this->registrationFields);
    }
}
