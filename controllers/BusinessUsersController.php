<?php

namespace app\controllers;

use Yii;
use app\models\Business;
use app\models\BusinessUserForm;
use app\models\search\UserSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * UsersController implements the CRUD actions for User model.
 */
class BusinessUsersController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['manageBusinessUsers'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'update'],
                        'roles' => ['salesReport', 'admin', 'receptionist']
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param integer $businessId
     * @return mixed
     */
    public function actionCreate($businessId)
    {
        $business = $this->findBusiness($businessId);
        $model = new BusinessUserForm(['scenario' => 'insert']);
        $model->business = $business;
        $model->authManager = Yii::$app->authManager;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'User successfully created.'));
            return $this->redirect(['businesses/view', 'id' => $business->id, 'tab' => 'users',]);
        }
        return $this->render('create', [
            'model' => $model,
            'business' => $business,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';
        $business = $model->business;

        if ($model->authManager->checkAccess($model->getUser()->getId(), 'salesReport')) {
            $model->canSeeSalesReport = true;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'User successfully updated.'));
            return $this->redirect(['businesses/view', 'id' => $business->id, 'tab' => 'users',]);
        }
        return $this->render('update', [
            'model' => $model,
            'business' => $business,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $business = $model->business;
        $model->delete();

        Yii::$app->session->setFlash('success', Yii::t('app', 'User successfully deleted.'));
        return $this->redirect(['businesses/view', 'id' => $business->id, 'tab' => 'users',]);
    }

    /**
     * Finds the BusinessUserForm model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BusinessUserForm the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BusinessUserForm::findOne($id)) !== null) {
            $model->authManager = Yii::$app->authManager;
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the Business model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Business the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findBusiness($id)
    {
        if (($model = Business::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
