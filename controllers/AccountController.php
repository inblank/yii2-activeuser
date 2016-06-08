<?php

namespace inblank\activeuser\controllers;

use inblank\activeuser\models\forms\LoginForm;
use inblank\activeuser\models\forms\RegisterForm;
use inblank\activeuser\models\forms\ResendForm;
use inblank\activeuser\models\User;
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
        /** @var RegisterForm $model */
        $model = Yii::createObject(RegisterForm::className());
        $view = 'register';
        if ($model->load(Yii::$app->getRequest()->post()) && $model->register()) {
            // congratulation and instruction
            $view = 'registerAfter';
        }
        return $this->render($view, [
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
     * User password restore action
     * @param string $hash hash for restore password
     */
    public function actionRestore($hash = null)
    {
        // TODO filter too many restore request
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
        /** @var User $user */
        $user = Yii::createObject(User::className())->findByToken($token);
        if ($user !== null && $user->confirm()) {
            return $this->render('confirm', ['user' => $user]);
        }
        return $this->render('confirmWrong');
    }
}
