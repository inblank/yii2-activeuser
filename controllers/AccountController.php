<?php

namespace inblank\activeuser\controllers;

use inblank\activeuser\models\forms\LoginForm;
use inblank\activeuser\models\forms\RegisterForm;
use inblank\activeuser\traits\CommonTrait;
use yii;
use yii\web\Controller;

class AccountController extends Controller{

    use CommonTrait;

    /**
     * User registering action
     */
    public function actionRegister(){
        /** @var RegisterForm $model */
        $model = Yii::createObject(RegisterForm::className());

        if ($model->load(Yii::$app->getRequest()->post()) && $model->register()) {
            return $this->goBack();
        }
        return $this->render('register', [
            'model' => $model,
            'module' => $this->getModule(),
        ]);
    }

    /**
     * User login action
     */
    public function actionLogin(){
        // TODO filter too many login request

        /** @var LoginForm $model */
        $model = Yii::createObject(LoginForm::className());

        if ($model->load(Yii::$app->getRequest()->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
            'module' => $this->getModule(),
        ]);
    }

    /**
     * User logout action
     */
    public function actionLogout(){

    }

    /**
     * User password restore action
     * @param string $hash hash for restore password
     */
    public function actionRestore($hash=null){
        // TODO filter too many restore request
    }

}
