<?php

namespace app\controllers;

use Yii;
use app\models\Business;
use app\models\Consumable;
use app\models\search\ConsumableSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * ConsumableController implements the CRUD actions for Consumable model.
 */
class ConsumableController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'create', 'update'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['manageConsumables'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'view'],
                        'roles' => ['receptionist', 'admin'],
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

    public function actionCreate($businessId)
    {
        $business = $this->findBusiness($businessId);
        $model = new Consumable;
        $model->business_id = $business->id;
        $post = null;

        if (isset($_POST['Consumable'])) {
           $post = Yii::$app->request->post();
           $post['Consumable']['shared_percentage'] = str_replace(['% ', ','], ['', '.'], $post['Consumable']['shared_percentage']);
        }

        if ($model->load($post) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Consumable successfully created.'));
            return $this->redirect(['consumable/view', 'id' => $model->id, 'tab' => 'consumables',]);
        }
        return $this->render('create', [
            'model' => $model,
            'business' => $business,
        ]);
    }
    /**
     * Displays a single Consumable model.
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
     * Updates an existing Consumable model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = null;
        if (isset($_POST['Consumable'])) {
           $post = Yii::$app->request->post();
           $post['Consumable']['shared_percentage'] = str_replace(['% ', ','], ['', '.'], $post['Consumable']['shared_percentage']);
           $post['Consumable']['shared_percentage_adm'] = str_replace(['% ', ','], ['', '.'], $post['Consumable']['shared_percentage_adm']);
        }

        if ($model->load($post) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Consumable successfully updated.'));
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Consumable model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $business = $model->business;
        $model->delete();

        Yii::$app->session->setFlash('success', Yii::t('app', 'Consumable successfully deleted.'));
        return $this->redirect(['businesses/view', 'id' => $business->id, 'tab' => 'consumables',]);
    }

    /**
     * Finds the Consumable model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Consumable the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Consumable::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findBusiness($id)
    {
        if (($model = Business::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

