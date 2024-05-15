<?php

namespace app\controllers;

use app\models\User;
use app\models\search\UserSearch;
use app\models\UserPasswordChangeForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * UsersController implements the CRUD actions for User model.
 */
class UsersController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['manageUsers', 'receptionist'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function alterPassword($user){
        $model = new UserPasswordChangeForm($user);
        $newPassword = $model->generatePassword(10, false);

        $model->newPassword = $newPassword;

        $model->savePassword();

        return $newPassword;
    }

    public function actionEmail($id)
    {
        $model = $this->findModel($id);

        Yii::$app->mailer->compose(
                'user/resend-welcome',
                [
                    'name' => $model->name,
                    'login' => $model->login,
                    'password' => $this->alterPassword($model)
                ]
            )
            ->setFrom(getenv('MAILER_FROM'))
            ->setTo($model->email)
            ->setSubject(Yii::t('app/mail', 'Welcome to TBest System!'))
            ->send()
        ;

        Yii::$app->session->setFlash('success', Yii::t('app', 'E-mail successfully re-sended'));
        return $this->redirect(['users/index']);
    }
}