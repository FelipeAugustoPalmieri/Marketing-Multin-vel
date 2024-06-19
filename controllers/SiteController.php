<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\ForgottenPasswordForm;
use app\models\LoginForm;
use app\models\User;
use app\models\UserPasswordChangeForm;
use app\models\UserPasswordResetForm;
use app\models\locasms\Envio;
use yii\db\ActiveRecord;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'index', 'change-password', 'forgot-my-password', 'reset-password'],
                'rules' => [
                    [
                        'actions' => ['logout', 'index', 'change-password'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['forgot-my-password', 'reset-password'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        $firstRole = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];

        if ($firstRole == 'business') {
            return $this->redirect(['sales/create']);
        }

        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'guest';
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionChangePassword()
    {
        $model = new UserPasswordChangeForm(Yii::$app->user->identity);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Password successfully changed.'));
            return $this->refresh();
        }
        return $this->render('change-password', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        $model = new UserPasswordResetForm($token);

        if (!$model->isValidToken()) {
            Yii::$app->session->setFlash('error', Yii::t('app/error', 'Invalid password reset link. Please request a new password reset.'));
            return $this->redirect(['forgot-my-password']);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash(
                'success',
                Yii::t('app', 'Now you can sign in with your account {login} and your new password.', ['login' => $model->getLogin()])
            );
            return $this->redirect(['login']);
        }
        return $this->render('reset-password', ['model' => $model]);
    }

    public function actionForgotMyPassword()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $params = Yii::$app->request->queryParams;
        
        $model = new ForgottenPasswordForm;
        if(isset($params['LoginForm'])){
            $model->login = $params['LoginForm']['username'];
        }

        if ($model->validate() && $model->save()) {
            $usuario = User::find()->where(['login'=>$model->login])->one();
            return [
                'status' => 'success',
                'msg' => Yii::t('app', 'Please follow the instructions we are sending to your email') . ' ' . $this->escondeEmail($usuario->email)
            ];
        }else{
            $msgRetorno = "";
            foreach ($model->errors as $key => $value) {
                if(strlen($value[0]) > 0){
                    $msgRetorno .= Yii::t('app', $value[0])."\n\r";
                }
            }
            return [
                'status' => 'error',
                'msg' => $msgRetorno
            ];
        }
    }

    private function escondeEmail($email){
        return substr($email, 0, 2)."***********".substr($email, strpos($email, '@'), (strlen($email) - (strpos($email, '@') - 1)));
    }

    public function actionTerms()
    {
        return $this->render('terms');
    }
}
