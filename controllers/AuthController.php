<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\LoginForm;
use app\models\SignupForm;

/**
 * Class AuthController
 * @package app\controllers
 */
class AuthController extends Controller{
    /**
     * Login action
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Signup action
     *
     * @return Response|string
     */
    public function actionSignup()
    {
        $model = new SignupForm();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            if ($model->signup()) {
                $this->redirect(['auth/login']);
            }
        }

        return $this->render('signup', ['model' => $model]);
    }
}
