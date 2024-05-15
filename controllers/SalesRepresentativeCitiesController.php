<?php

namespace app\controllers;

use Yii;
use app\models\SalesRepresentativeCity;
use app\models\Consumer;
use app\models\City;
use app\models\search\SalesRepresentativeCitySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class SalesRepresentativeCitiesController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'get-cities', 'delete'],
                        'roles' => ['manageRepresentativeCities', 'receptionist'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['get-cities'],
                        'roles' => ['viewRepresentativeCities', 'receptionist'],
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

    public function actionCreate($consumerId)
    {
        $consumer = $this->findConsumer($consumerId);
        $model = new SalesRepresentativeCity;
        $model->sales_representative_id = $consumer->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'City successfully created.'));
            return $this->redirect(['consumers/view', 'id' => $consumer->id, 'tab' => 'representative',]);
        }
        return $this->render('create', [
            'model' => $model,
            'consumer' => $consumer,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        Yii::$app->session->setFlash('success', Yii::t('app', 'City successfully deleted.'));
        return $this->redirect(['consumers/view', 'id' => $model->sales_representative_id, 'tab' => 'representative',]);
    }

    public function actionGetCities($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $consumer = Consumer::findOne(intval($id));
        if (!$consumer instanceof Consumer) {
            exit;
        }

        $array = [];

        $city = SalesRepresentativeCity::find()
            ->andWhere('sales_representative_id = :id', [':id' => $consumer->id])
            ->orderBy("city_id ASC")
            ->all();

        foreach ($city as $city) {
            $array[$city->city_id] = $city->city->name . '-' . $city->city->state->abbreviation;
        }

        return $array;
    }

    protected function findModel($id)
    {
        if (($model = SalesRepresentativeCity::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findConsumer($id)
    {
        if (($model = Consumer::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
