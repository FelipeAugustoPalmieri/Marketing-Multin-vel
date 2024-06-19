<?php

namespace app\controllers;

use Yii;
use yii\helpers\Url;
use app\models\Sale;
use app\models\User;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Business;
use app\models\Consumable;
use app\models\SalesReport;
use app\models\Configuration;
use app\models\Consumer;
use yii\db\Query;




/**
 * SalesController implements the CRUD actions for Sale model.
 */
class SalesController extends Controller 
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'consumers', 'get-consumables', 'get-coperchap', 'get-documento-investimento'],
                        'roles' => ['submitSales', 'receptionist', 'admin'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['report', 'report-export'],
                        'roles' => ['salesReport', 'admin'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['report-export', 'update-data', 'update-total'],
                        'roles' => ['admin'],
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

    public function actionCreate()
    {
        $model = new Sale();

        $post = null;
        if (isset($_POST['Sale'])) {
           $post = Yii::$app->request->post();
           $post['Sale']['total'] = str_replace(['R$ ', ','], ['', '.'], $post['Sale']['total']);
        }
        if(isset($post['Sale']['consumer_sale_id']) && $post['Sale']['consumer_sale_id'] > 0 && $post['Sale']['business_id'] == Configuration::getConfigurationValue(Configuration::ID_CONVENIO_INVESTIMENTO)){
            $model->consumer_sale_id = $post['Sale']['consumer_sale_id'];
        }else{
            $model->consumer_sale_id = 0;
        }
        $mostrarpopup = false;
        try
        {
            if ($model->load($post) && $model->validate() && $model->save()) {
                $this->sendProofOfPurchase($model);
                Yii::$app->session->setFlash('success', Yii::t('app', 'Sale successfully created.'));
                return $this->redirect('create');
            } else {
                if (!Yii::$app->user->can('admin')) {
                    $model->business_id = Yii::$app->user->identity->authenticable_id;
                    $business = Business::findOne((Yii::$app->user->can('receptionist')? 1 : Yii::$app->user->identity->authenticable_id));
                    if($business->whatsapp == "" || $business->legalPerson->comercial_phone == ""){
                        $mostrarpopup = true;
                    }
                }
                return $this->render('create', [
                    'model' => $model,
                    'mostrarpopup' => $mostrarpopup,
                ]);
            }
        }catch (Exception $e){
            echo '<pre>'; print_r($e->getMessage()); echo '</pre>';
        }
        
    }

    public function actionUpdateData()
    {
        try
        {
            if(Yii::$app->request->post()){
                $post = Yii::$app->request->post();
                if(isset($post['pk']) && isset($post['value'])){
                    $model = new Sale();

                    if(strlen($post['value']) < 11){
                        $post['value'] = $post['value']." 01:01:01";
                    }

                    $post['value'] = substr($post['value'], 6, 4).'-'.substr($post['value'], 3, 2).'-'.substr($post['value'], 0, 2).' '.substr($post['value'], 11, 8);

                    $model->UpdateDateSoad($post['value'], (int)$post['pk']);
                }
            }
        }catch(Exception $e){
            echo '<pre>'; print_r($e->getMessage()); echo '</pre>';
        }
    }

    public function actionUpdateTotal()
    {
        try
        {
            if(Yii::$app->request->post()){
                $post = Yii::$app->request->post();
                if(isset($post['pk']) && isset($post['value'])){
                    $model = $this->findModel((int)$post['pk']);

                    $pattern = "@([0-9]+,[0-9]{2})@";
                    preg_match_all($pattern, $post['value'], $retornovalor);
                    $model->total = str_replace(',', '.', $retornovalor[0][0]);

                    $model->UpdateTotal();
                    $retorno = array(
                        'repasse' => Yii::$app->formatter->asCurrency($model->calculateFees()),
                        'id' => $model->id
                    );

                    return json_encode($retorno);
                }
            }
        }catch(Exception $e){
            echo '<pre>'; print_r($e->getMessage()); echo '</pre>';
        }   
    }

    public function actionReportExport()
    {
        $model = new SalesReport;
        if(isset($_GET) && $_GET['SalesReport']['convenio'] == ""){
            unset($_GET['SalesReport']['convenio']);
        }
        $model->load($_GET);

        $dataProvider = $model->getSalesReport();
        $dataProvider->pagination  = false;
        $dataProvider->sort = false;
        
        if (Yii::$app->request->get() && isset(Yii::$app->request->get('SalesReport')['convenio'])){
            $modelbusiness = $this->findModelBusiness(Yii::$app->request->get('SalesReport')['convenio']);
            $legalPerson = $modelbusiness->legalPerson;
        }
        $dados = array(
            'dataInicial' => Yii::$app->formatter->asDate($model->inicio_periodo,'dd/MM/yyyy'),
            'dataFinal' => Yii::$app->formatter->asDate($model->fim_periodo,'dd/MM/yyyy'),
            'valorTotal' => Yii::$app->formatter->asCurrency(SalesReport::getTotal($model->inicio_periodo, $model->fim_periodo, $model->convenio)),
            'valorRemessa' => Yii::$app->formatter->asCurrency(SalesReport::getTotalFees($model->inicio_periodo, $model->fim_periodo, $model->convenio)),
            'count' => SalesReport::getTotalRows($model->inicio_periodo, $model->fim_periodo, $model->convenio)
        );
        $content = $this->renderPartial('report-export', [
            'dados' => $dados,
            'model' => $model,
            'legalPerson' => isset($legalPerson)? $legalPerson : null,
            'dataProvider' => $dataProvider,
        ]);

        $pdf = Yii::$app->pdf;
        $pdf->filename = Yii::t('app', 'Sales Report') . ' - TBest.pdf';
        $pdf->options = ['title'=> Yii::t('app', 'Sales Report') . ' - TBest'];
        $pdf->content = $content;
        $pdf->methods = [
            'SetHeader'=>[
                '<div style="text-align: left">
                    <img width="25px" src="' . Url::base('http') . '/images/newlogotipo-tbest-login.png" alt="Tbest">' . 'TBest - ' . Yii::t('app', 'Smart Consumption') .
                '</div>'
            ],
            'SetFooter'=>['{PAGENO}']
        ];

        //$pdf->output();

        return $pdf->render(); 
    }

    public function actionReport()
    {
        $model = new SalesReport;
        $model->load($_GET);
        
        if (!Yii::$app->user->can('admin')) {
            $business_id = Yii::$app->user->identity->authenticable_id;
            $model->convenio = $business_id;
        }

        if ($model->convenio)
            $model->businessObject = Business::findOne($model->convenio);

        return $this->render('report', [
            'model' => $model,
            'generateCsv' => Yii::$app->request->get('export')
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Sale::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function findModelBusiness($id){
        if (($model = Business::findOne($id)) !== null) {
            return $model;
        }
    }

    public function actionConsumers($q = null, $id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select('id, name AS text')
                ->from('consumers')
                ->where(['like', 'name', $q])
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Consumer::find($id)->name];
        }
        return $out;
    }

    public function actionGetCoperchap($id){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        return ($id == Configuration::getConfigurationValue(Configuration::ID_CONVENIO_INVESTIMENTO) ? true : false);
    }

    public function actionGetDocumentoInvestimento($consumerid){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $consumer = new Consumer;
        $consumer = $consumer->findOne($consumerid);
        $data = date('my');
        $name = \strtolower(substr($consumer->legalPerson->name, 0, strpos($consumer->legalPerson->name, " ")));
        return "invest-".$name.$data;
    }

    public function actionGetConsumables($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $business = Business::findOne(intval($id));
        if (!$business instanceof Business) {
            exit;
        }

        $array = [];

        $consumable = Consumable::find()
            ->ofBusiness($business->id)
            ->orderBy("description ASC")
            ->all();

        foreach ($consumable as $consumable) {
            $array[$consumable->id] = $consumable->description;
        }

        return $array;
    }

    private function sendProofOfPurchase(Sale $sale)
{
    $user = User::find()
        ->where(['authenticable_type' => 'Consumer'])
        ->andWhere(['authenticable_id' => $sale->consumer_id])
        ->one();

    if (!$user) {
        return false;
    }

    $mailer = \Yii::$app->mailer;
    $mailer->htmlLayout = 'layouts/purchase';

    return $mailer->compose('sale/proof-purchase', ['sale' => $sale])
        ->setFrom(getenv('MAILER_FROM'))
        ->setTo($user->email)
        ->setSubject(Yii::t('app/mail', 'Proof Of Purchase'))
        ->send();
 }
}
