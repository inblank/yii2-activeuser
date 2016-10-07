<?php

namespace inblank\activeuser\controllers;

use inblank\activeuser\models\forms\LoginForm;
use inblank\activeuser\models\forms\RegisterForm;
use inblank\activeuser\models\forms\ResendForm;
use inblank\activeuser\models\forms\RestoreForm;
use inblank\activeuser\traits\CommonTrait;
use yii;
use yii\web\Controller;

class AccountController extends Controller
{

    use CommonTrait;

    /**
     * User registering action
     */
    public function actionRegister()
    {
        if (!$this->module->isRegistrationEnabled()) {
            return $this->render('registerDisable');
        }
        $flashMessageId = 'activeuser_register';

        $email = Yii::$app->session->getFlash($flashMessageId);
        if (!empty($email)) {
            return $this->render('registerAfter', [
                'user' => Yii::createObject(self::di('User'))->findOne(['email' => $email]),
            ]);
        }
        /** @var RegisterForm $model */
        $model = Yii::createObject(RegisterForm::className());
        if ($model->load(Yii::$app->getRequest()->post()) && $model->register()) {
            // congratulation and instruction
            Yii::$app->session->setFlash($flashMessageId, $model->email);
            return $this->redirect(['/activeuser/account/register']);
        }
        return $this->render('register', [
            'model' => $model,
        ]);
    }

    /**
     * User login action
     */
    public function actionLogin()
    {
        // TODO filter too many login request
        /** @var LoginForm $model */
        $model = Yii::createObject(LoginForm::className());
        if ($model->load(Yii::$app->getRequest()->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * User logout action
     */
    public function actionLogout()
    {

    }

    /**
     * Request password change
     * @return string|yii\web\Response
     * @throws yii\base\InvalidConfigException
     * @throws yii\web\NotFoundHttpException
     */
    public function actionRestore()
    {
        // TODO filter too many restore request and set in Module period for restore
        if (!$this->getModule()->enablePasswordRestore) {
            throw new yii\web\NotFoundHttpException();
        }
        $flashMessageId = 'activeuser_restore_sent';
        $email = Yii::$app->session->getFlash($flashMessageId);
        if ($email !== null) {
            // message with instructions sent
            return $this->render('restoreSent', [
                'email' => $email,
            ]);
        }

        /** @var RestoreForm $model */
        $model = Yii::createObject(RestoreForm::className());
        $model->setScenario($model::SCENARIO_EMAIL);
        if ($model->load(Yii::$app->getRequest()->post()) && $model->restore()) {
            Yii::$app->session->setFlash($flashMessageId, $model->email);
            return $this->redirect(['/activeuser/account/restore']);
        }
        $error = Yii::$app->session->getFlash('activeuser_error');
        if (!empty($error) && !empty($error['token'])) {
            $error = $error['token'][0];
        } else {
            $error = null;
        }
        return $this->render('restore', [
            'model' => $model,
            'error' => $error,
        ]);
    }

    /**
     * Change user password
     * @param string $token user token for restore
     * @return string|yii\web\Response
     * @throws yii\base\InvalidConfigException
     * @throws yii\web\NotFoundHttpException
     */
    public function actionPassword($token = null)
    {
        $flashMessageId = 'activeuser_restore';

        $email = Yii::$app->session->getFlash($flashMessageId);
        if (!empty($email)) {
            // congratulation message
            return $this->render('restoreComplete', [
                'user' => Yii::createObject(self::di('User'))->findOne(['email' => $email]),
            ]);
        }

        /** @var \inblank\activeuser\models\User $user */
        if (!$this->getModule()->enablePasswordRestore || $token === null || !($user = Yii::createObject(self::di('User'))->findByToken($token))) {
            throw new yii\web\NotFoundHttpException();
        }

        if ($user->isRestoreTokenExpired()) {
            Yii::$app->session->setFlash('activeuser_error', ['token' => Yii::t('activeuser_general', 'You token was expired')]);
            return $this->redirect(['/activeuser/account/restore']);
        }

        /** @var RestoreForm $model */
        $model = Yii::createObject(RestoreForm::className());
        $model->setScenario($model::SCENARIO_PASSWORD);
        if ($this->getModule()->generatePassOnRestore) {
            // password auto generation
            $changed = $user->changePassword();
        } else {
            // change to user entered
            $model->email = $user->email;
            $changed = $model->load(Yii::$app->getRequest()->post()) && $model->changePassword();
        }
        if ($changed) {
            Yii::$app->session->setFlash($flashMessageId, $user->email);
            return $this->redirect(['/activeuser/account/password']);
        }
        return $this->render('restorePass', [
            'model' => $model
        ]);
    }

    /**
     * User resend confirmation message
     */
    public function actionResend()
    {
        // TODO filter too many resend request and set in Module period for resend
        if(!$this->getModule()->enableConfirmation){
            throw new yii\web\NotFoundHttpException();
        }

        $email = Yii::$app->session->getFlash('resend');
        if($email!==null){
            // already sent
            return $this->render('resendComplete', [
                'email' => $email,
            ]);
        }

        // new resend
        /** @var ResendForm $model */
        $model = Yii::createObject(ResendForm::className());
        if ($model->load(Yii::$app->getRequest()->post()) && $model->resend()) {
            // resend complete, even if the user is not found
            Yii::$app->session->setFlash('resend', $model->email);
            return $this->redirect(['/activeuser/account/resend']);
        }
        return $this->render('resend', [
            'model' => $model,
        ]);
    }

    /**
     * Confirm email page
     * @param string $token user token for confirm email
     * @return string
     * @throws yii\base\InvalidConfigException
     */
    public function actionConfirm($token = '')
    {
        /** @var \inblank\activeuser\models\User $user */
        $user = Yii::createObject(self::di('User'))->findByToken($token);
        if ($user !== null && $user->confirm()) {
            return $this->render('confirm', ['user' => $user]);
        }
        return $this->render('confirmWrong');
    }

}
