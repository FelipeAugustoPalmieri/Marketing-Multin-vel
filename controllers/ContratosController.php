<?php

namespace app\controllers;

use app\models\Business;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\Contratos;
use app\models\ListContracts;
use app\models\search\ContratosSearch;
use app\models\Investimento;
use app\models\Configuration;
use kartik\mpdf\Pdf;
/**
 * SalesController implements the CRUD actions for Sale model.
 */
class ContratosController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create', 'update', 'delete', 'visualizar'],
                        'roles' => ['admin'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['submitSales'],
                    ]
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
     * Lists all Consumer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $isAdmin = in_array('admin', array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)));
        if (!$isAdmin) {
            throw new \Exception(Yii::t('app', 'You are not authorized to perform this action.'));
        }
        $model = new Contratos();
        $searchModel = new ContratosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new Contratos();

        if (Yii::$app->request->isPost) {
            if($model->load(Yii::$app->request->post()) && $model->save())
            
            Yii::$app->session->setFlash('success', Yii::t('app', 'Contract successfully created.'));
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Contract successfully updated.'));
            return $this->redirect('index');
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', Yii::t('app', 'Contract successfully deleted.'));
        return $this->redirect(['index']);
    }

    public function actionView()
    {
        $dados = ListContracts::find()->Where(["object_type"=>Yii::$app->user->identity->authenticable_type])->andWhere(["object_id"=>Yii::$app->user->identity->authenticable_id])->andWhere(["is_cancel"=>0])->one();
        if(!$dados){
            Yii::$app->session->setFlash('error', Yii::t('app', 'There is no contract generated yet, please contact the city representative.'));
            return $this->redirect(['sales/create']);
        }else{
            $pdf = Yii::$app->pdf;
            $pdf->filename = Yii::t('app', 'Contract the bonus transfer') . ' - TBest.pdf';
            $pdf->content = $dados->contract;
            $pdf->methods = [
                'SetHeader'=>[
                    '<div style="text-align: left">
                        <img width="25px" src="https://sistema.tbest.com.br/images/newlogotipo-tbest-login.png" alt="Tbest">' . 'TBest - ' . Yii::t('app', 'Smart Consumption') .
                    '</div>'
                ],
                'SetFooter'=>['{PAGENO}']
            ];

            return $pdf->render();
        }
    }

    public function actionVisualizar($id){
        $model = $this->findModel($id);
        if($model->flag_local == 1){
            $investimento = new Investimento();
            $investimento->consumer_id = Yii::$app->user->identity->consumer->id;
            $investimento->dia_vencimento = 1;
            $investimento->prazo = 60;
            $investimento->primeira_parcela = false;
            $investimento->valor = Configuration::getConfigurationValue(Configuration::VALOR_INVESTIMENTO);
            $investimento->GerarContratoInvestimento(Pdf::DEST_BROWSER, null, true);
        }
    }

    protected function findModel($id)
    {
        if (($model = Contratos::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}