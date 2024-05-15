<?php

namespace app\controllers;

use Yii;
use app\models\Consumer;
use app\models\LegalPerson;
use app\models\PhysicalPerson;
use app\models\Plane;
use app\models\PlanoInvestimento;
use app\models\search\ConsumerSearch;
use app\models\asaas\Costumers;
use app\models\asaas\Payments;
use app\models\bling\ContatoBling;
use app\models\bling\ContaReceberBling;
use app\models\search\RepresentativeReportSearch;
use app\models\search\RepresentativeComissionSearch;
use app\models\search\SalesRepresentativeCitySearch;
use app\models\search\NetworkReportSearch;
use app\models\Configuration;
use app\models\RepresentativeComission;
use app\models\TransactionReport;
use app\models\User;
use app\models\Faturamento;
use app\models\financeiro\ContasReceber;
use app\models\financeiro\ContasReceberParcelas;
use Exception;
use Hashids\Hashids;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\helpers\Url;
use app\models\Contratos;
use app\models\Investimento;
use kartik\mpdf\Pdf;

/**
 * ConsumersController implements the CRUD actions for Consumer model.
 */
class ConsumersController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'create', 'update', 'active','report', 'representative-report', 'representative-report-export', 'network-report', 'disable', 'view-disable', 'rearrange', 'processar-consumers-investimento', 'processar-contato-bling', 'termos'],
                'rules' => [
                    [
                        'actions' => ['termos'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['view','cadastro-fora', 'pagamento', 'processar-pagamento', 'gerar-contrato-investimento', 'download-investimento'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'update', 'active', 'disable'],
                        'roles' => ['adminManageConsumers'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view', 'create', 'update'],
                        'roles' => ['manageConsumers'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['report'],
                        'roles' => ['transactionReport'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['representative-report', 'representative-report-export'],
                        'roles' => ['viewRepresentativeCities', 'receptionist', 'admin'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['network-report'],
                        'roles' => ['viewNetworkReport'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['extract','report-export', 'representative-comission-export', 'view-disable', 'rearrange', 'cadastrar-asaas', 'processar-consumers-investimento', 'processar-contato-bling'],
                        'roles' => ['admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'activate' => ['post'],
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

        $searchModel = new ConsumerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,

        ]);
    }

    public function actionView($id, $tab = null)
    {
        $isAdmin = in_array('admin', array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)));

        if (!$isAdmin){
            $consumerId = Yii::$app->user->getIdentity()->consumer->id;
            if ($consumerId != $id){
                Yii::$app->session->setFlash('error', Yii::t('app', 'You are not authorized to perform this action.'));
                return $this->redirect(['site/index']);
            }
        }

        $salesRepresentativeCitySearchModel = new SalesRepresentativeCitySearch();
        $salesRepresentativeCitySearchModel->sales_representative_id = $id;
        $salesRepresentativeCityDataProvider = $salesRepresentativeCitySearchModel->search(Yii::$app->request->queryParams);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'salesRepresentativeCityDataProvider' => $salesRepresentativeCityDataProvider,
            'tab' => $tab,
        ]);
    }

    /**
     * Creates a new Consumer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $consumer = new Consumer;
        $plane = new Plane;
        $planoinvestimento = new PlanoInvestimento;
        $legalPerson = new LegalPerson(['scenario' => 'insert']);
        $physicalPerson = new PhysicalPerson(['scenario' => 'consumer']);

        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();

            if($post != null && $post['Plane']['pay_plane'] != ''){
                $consumer->plane_id = Plane::findOne(['pay_plane'=>$post['Plane']['pay_plane']])->id;
                $plane = Plane::findOne(['id'=>$consumer->plane_id]);
            }

            if($post != null && isset($post['PlanoInvestimento']['id']) && $post['PlanoInvestimento']['id'] != ''){
                $consumer->plane_investiment_id = $post['PlanoInvestimento']['id'];
                $planoinvestimento = PlanoInvestimento::findOne(['id'=>$consumer->plane_id]);
            }

            if(isset($post['Consumer']['maximum_amount']) && $post['Consumer']['maximum_amount'] > 1){
                
                if(strpos($post['Consumer']['maximum_amount'], 'Anos') !== false){
                    $parcelasnr = (intval(preg_replace("/[^0-9]/", "", $post['Consumer']['maximum_amount'])) * 12);
                }else{
                    $parcelasnr = intval($post['Consumer']['maximum_amount']);
                }
                $consumer->maximum_amount = $parcelasnr;
                $consumer->percentage_plot = Configuration::getConfigurationValue(Configuration::ID_PERCENTUAL_PARCELAS);
            }else if(isset($post['Consumer']['maximum_amount']) && $post['Consumer']['maximum_amount'] == "00"){
                $consumer->maximum_amount = 0;
                $consumer->percentage_plot = Configuration::getConfigurationValue(Configuration::ID_PERCENTUAL_PARCELAS);
            }
        
            if ($consumer->load($post) & $legalPerson->load($post) & $physicalPerson->load($post)) {
                $transaction = Yii::$app->db->beginTransaction();

                // Se já existe um convênio com esse CPF, usa os mesmos dados como base
                if ($physicalPersonExistente = PhysicalPerson::findOne(['cpf' => $physicalPerson->cpf])) {
                    $physicalPerson = $physicalPersonExistente;
                    $legalPerson = LegalPerson::findOne(['person_class' => 'PhysicalPerson', 'person_id' => $physicalPersonExistente->id]);
                    $legalPerson->load($post);
                    $physicalPerson->load($post);
                }

                if ($physicalPerson->save()) {
                    $legalPerson->person_class = 'PhysicalPerson';
                    $legalPerson->person_id = $physicalPerson->id;
                                        
                    if ($legalPerson->validate() && $legalPerson->save()) {
                        $consumer->legal_person_id = $legalPerson->id;

                        if ($consumer->save()) {
                            $consumer->trigger(Consumer::EVENT_SET_REPRESENTATIVE_PERMISSION);
                            $transaction->commit();
                            $dadosapi = new Costumers();
                            $dadosapibling = new ContatoBling();
                            
                            $this->DadosParaAsaasCustomers($dadosapi, $consumer);

                            $dadosapi->preparaCliente($consumer);

                            $dadosapibling->prepararCliente($consumer);

                            /*$this->createPagamento(
                                $plane, 
                                $consumer, 
                                $consumer->maximum_amount > 0 ? $consumer->maximum_amount : 1, 
                                ['consumers/create', 'id' => $consumer->id]
                            );*/

                            Yii::$app->session->setFlash('success', Yii::t('app', 'Consumer successfully created.'));
                            return $this->redirect(['site/index']);
                        }else{
                            $physicalPerson->isNewRecord = true;                                                    
                            foreach ($consumer->errors as $key => $value) {
                                if(strlen($value[0]) > 0){
                                    Yii::$app->session->setFlash('error', Yii::t('app', $value[0]));
                                }
                            }                            
                        }
                    }else{                        
                        $physicalPerson->isNewRecord = true;
                        foreach ($legalPerson->errors as $key => $value) {
                            if(strlen($value[0]) > 0){
                                Yii::$app->session->setFlash('error', Yii::t('app', $value[0]));
                            }
                        }
                    }
                }else{
                    $physicalPerson->isNewRecord = true;
                    foreach ($physicalPerson->errors as $key => $value) {
                        if(strlen($value[0]) > 0){
                            Yii::$app->session->setFlash('error', Yii::t('app', $value[0]));
                        }
                    }
                }

                $transaction->rollBack();
            }
        }

        return $this->render('create', [
            'consumer' => $consumer,
            'plane' => $plane,
            'planoinvestimento' => $planoinvestimento,
            'legalPerson' => $legalPerson,
            'physicalPerson' => $physicalPerson,
        ]);
    }


    /**
     * Updates an existing Consumer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $consumer = $this->findModel($id);

        $isAdmin = in_array('admin', array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)));
        if (!$isAdmin && $consumer->id != Yii::$app->user->getIdentity()->consumer->id) {
            throw new \Exception(Yii::t('app', 'You are not authorized to perform this action.'));
        }

        $legalPerson = $consumer->legalPerson;
        $physicalPerson = $legalPerson->person;
        
        $post = Yii::$app->request->post();

        $legalPerson->scenario = 'update';
        $physicalPerson->scenario = $isAdmin ? 'default':'consumer';

        $plane = Plane::findOne(['id'=>$consumer->plane_id]);
        
        $planoinvestimento = $consumer->plane_investiment_id > 0 ? PlanoInvestimento::findOne(['id'=>$consumer->plane_investiment_id]) : New PlanoInvestimento();

        $user = User::findOne(['authenticable_id' => $consumer->id, 'authenticable_type' => 'Consumer']);

        if($isAdmin && $post != null && $post['Plane']['pay_plane'] != ''){
            $consumer->plane_id = Plane::findOne(['pay_plane'=>$post['Plane']['pay_plane']])->id;
        }

        if($isAdmin && $post != null && $post['PlanoInvestimento']['id'] != ''){
            $consumer->plane_investiment_id = $post['PlanoInvestimento']['id'];
        }
        
        if(isset($post['Consumer']['maximum_amount'])){
            $consumer->maximum_amount = intval($post['Consumer']['maximum_amount']);
            $consumer->percentage_plot = Configuration::getConfigurationValue(Configuration::ID_PERCENTUAL_PARCELAS);
        }

        if ($consumer->load($post) & $legalPerson->load($post) & $physicalPerson->load($post)) {
            $transaction = Yii::$app->db->beginTransaction();

            if ($consumer->save() && $legalPerson->save() && $physicalPerson->save()) {

                if (isset($user)) {
                    $user->email = $legalPerson->email;
                    $user->name = $physicalPerson->name;

                    if (!$user->save()) {
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('error', Yii::t('app/error', 'Problem updating consumer'));
                    }
                }

                $consumer->trigger(Consumer::EVENT_SET_REPRESENTATIVE_PERMISSION);
                $transaction->commit();
                Yii::$app->session->setFlash('success', Yii::t('app', 'Consumer successfully updated.'));
                return $this->redirect(['view', 'id' => $consumer->id]);
            }

            $transaction->rollBack();
            Yii::$app->session->setFlash('error', Yii::t('app/error', 'Problem updating consumer'));
        }

        return $this->render('update', [
            'consumer' => $consumer,
            'plane' => $plane,
            'planoinvestimento' => $planoinvestimento,
            'legalPerson' => $legalPerson,
            'physicalPerson' => $physicalPerson,
        ]);
    }

    /**
     * Deletes an existing Consumer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $consumer = $this->findModel($id);
            $legalPerson = $consumer->legalPerson;
            $legalPersonType = $legalPerson->person;

            if ($consumer->getChildrenConsumers()->count() > 0) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', Yii::t('app', 'Remove children consumers before deleting.'));
                return $this->redirect(['view', 'id' => $consumer->id]);
            }

            if ($consumer->delete() && $legalPerson->delete() && $legalPersonType->delete()) {
                $transaction->commit();
                Yii::$app->session->setFlash('success', Yii::t('app', 'Consumer successfully deleted.'));
                return $this->redirect(['index']);
            }

            $transaction->rollBack();
            Yii::$app->session->setFlash('error', Yii::t('app', 'An error occurred while deleting this consumer.'));
            return $this->redirect(['view', 'id' => $consumer->id]);

        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function actionActive($id)
    {
        $consumer = $this->findModel($id);

        $consumer->identifier = ($consumer->identifier) ? $consumer->identifier : $consumer->getNextFreeIdentifier();

        if ($consumer->paid_affiliation_fee) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Consumer has been activated before.'));
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->post()) {

            $transaction = Yii::$app->db->beginTransaction();

            $consumer->plane_id = ($consumer->plane_id) ? $consumer->plane_id : Yii::$app->request->post('Consumer')['plane_id'];
            $consumer->identifier = Yii::$app->request->post('Consumer')['identifier'];
            $consumer->paid_affiliation_fee = true;

            if ($consumer->validate()) {
                if ($consumer->save() && $consumer->activate()) {
                    $consumer->trigger(Consumer::EVENT_SET_REPRESENTATIVE_PERMISSION);
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Consumer successfully activated. He or she will receive sign in instructions by e-mail.'));
                    return $this->redirect(['index']);
                }

                $transaction->rollBack();
                Yii::$app->session->setFlash('error', Yii::t('app', 'An unknown error occorred while activating the consumer.'));
                return $this->redirect(['index']);
            }
        }

        return $this->render('active', [
            'model' => $consumer,
            'consumer' => $consumer = $this->findModel($id),
        ]);
    }

    public function actionViewDisable($id){
        $consumer = $this->findModel($id);

        //Mostrar qual são seus filhos.
        $filhos = $consumer->getChildrenConsumers()->all();
        $dadosFilhos = array();

        if($filhos){
            foreach($filhos as $filho){
                if(isset($filho->id)){
                    $dadosFilhos[] = $filho->identifier.' - '.$filho->legalPerson->name . ' - ' . Yii::t('app', ucfirst($filho->position).' position');
                }
            }
        }

        //Mostrar qual é seu pai (caso exista).
        $pai = $consumer->parentConsumer;

        return json_encode(array(
            'pai'=> (isset($pai->legalPerson->name))? $pai->identifier.' - '.$pai->legalPerson->name : '',
            'consumidor' => $consumer->legalPerson->name,
            'filhos' => $dadosFilhos,
        ));
    }

    public function actionRearrange(){
        $consumer = new Consumer();

        $Lista = $consumer->find()->where("is_disabled = 0")->orderBy(['identifier'=>SORT_ASC])->all();

        $pai = array();
        $contador = 0;
        $contadorPai = 0;
        $totalgeral = count($Lista);
        if(count($pai) == 0){
            $pai[$contadorPai] = $Lista[$contador]->identifier;
            //unset($Lista[$contador]);
            $contador = 1;
        }
        $auxcontador = 1;
        echo 'Total Geral: '.$totalgeral.'<br>';
        /*do {
            echo '+++++++++++++++ Contador: '.$contador.' Contador Pai:'.$contadorPai.' ++++++++++++++++++++<br>';
            if($contadorPai > 4){
                echo '<pre>';
                print_r($pai);
                echo '</pre>';
                exit();
            }
            echo 'Pai: '.$contadorPai.' - '.$pai[$contadorPai].'<br>';
            $contador = $contador*2;
            echo 'filhos:<br>';
            for($i = $auxcontador; $i <= $contador; $i++){
                echo $Lista[$i]->identifier.'-'.$Lista[$i]->legalPerson->name.'<br>';
                $pai[$i] = $Lista[$i]->identifier;
                unset($Lista[$i]);
                unset($pai[$contadorPai]);
            }
            $auxcontador = $auxcontador+$contador;
            $contadorPai = $contadorPai+1;
            $totalgeral = $totalgeral - $contador;
        } while ($totalgeral > 0);*/

        foreach($Lista as $cons){
            if(isset($cons)){
                echo '<pre>';
                echo 'Codigo:'.(isset($cons->identifier)? $cons->identifier : 0).' Nome: '.(isset($cons->legalPerson)? isset($cons->legalPerson->name)? $cons->legalPerson->name : '' : '').'<br>';
                echo '</pre>';
            }
        }

        $this->render('rearrange', []);
    }

    public function actionDisable($id){
        try{
            $consumer = $this->findModel($id);
            if($consumer->disabled()){
                $this->redirect(['index']);
            }else{
                return json_encode(array('errors'=> $consumer->getErrors()));
            }
        }catch(Exception $e){
            return json_encode(array('msg' => $e->getMessage()));
        }

    }

    public function actionReport()
    {
        $model = new TransactionReport;
        $model->load($_GET);
        $model->user = \Yii::$app->user->getIdentity();

        $testedata = $model->getTransactionReport();

        //print_r($testedata);

        return $this->render('report', [
            'model' => $model,
            'generateCsv' => Yii::$app->request->get('export')
        ]);
    }

    public function actionReportExport(){
        $model = new Consumer;
        $modelplano = null;
        $get = null;
        $get = Yii::$app->request->get();

        //$model->user = \Yii::$app->user->getIdentity();
        $informationsConsumers = [];
            $ordem = [];

        list($ano, $mes) = explode("-", date("Y-m"));
        $lastDay = date("t", mktime(0,0,0,$mes,'01',$ano));

        if (isset($get["TransactionReport"]["inicio_periodo"]) && !empty($get["TransactionReport"]["inicio_periodo"])){
            $startDate = new \DateTime($get["TransactionReport"]["inicio_periodo"]);
        }else{
            $startDate = new \DateTime($ano.'-'.$mes.'-01 00:00:01');
        }
        if(isset($get["TransactionReport"]["fim_periodo"]) && !empty($get["TransactionReport"]["fim_periodo"])){
            $endDate = new \DateTime($get["TransactionReport"]["fim_periodo"]);
        }else{
            $endDate = new \DateTime($ano.'-'.$mes.'-'.$lastDay.' 23:59:59');
        }

        $pattern = "@([0-9]+,[0-9]{2})@";
        preg_match_all($pattern, $get['TransactionReport']['minimovalor'], $retornovalor);
        if(isset($retornovalor[0][0])){
            $valorMinimo = str_replace(',', '.', $retornovalor[0][0]);
        }else{
            $valorMinimo = "0";
        }
        $consumer = 0;
        if(isset($get["TransactionReport"]["consumer_id"]) && $get["TransactionReport"]["consumer_id"] > 0){
            $consumer = (int)$get["TransactionReport"]["consumer_id"];
        }

        $retornoConsumer = $model->getConsumersPlane($startDate, $endDate, 0, $valorMinimo, $consumer);

        if(isset($get["TransactionReport"]["xOrderPlanos"]) && $get["TransactionReport"]["xOrderPlanos"] == 1){
            $modelplano = Plane::find()->all();
        }

        if($modelplano != null){
            foreach ($modelplano as $key => $value) {
                $ordem[] = array(
                    'tipo' => 'planos',
                    'titulo' => "Plano ".$value->name_plane,
                    'periodo' => \Yii::$app->formatter->asDatetime($startDate, 'dd/MM/yyyy')." até ".\Yii::$app->formatter->asDatetime($endDate, 'dd/MM/yyyy'),
                    'dados' => $model->getConsumersPlane($startDate, $endDate, $value->id, $valorMinimo, $consumer)
                );
            }
        }else{
            $ordem[] = array(
                'tipo' => 'nenhum',
                'titulo' => "Consumidores",
                'periodo' => \Yii::$app->formatter->asDatetime($startDate, 'dd/MM/yyyy')." até ".\Yii::$app->formatter->asDatetime($endDate, 'dd/MM/yyyy'),
                'dados' => $retornoConsumer
            );
        }

        $content = $this->renderPartial('report-export', [
            'model' => $model,
            'xInfConta' => isset($get["TransactionReport"]["xInfContaTotal"]) && $get["TransactionReport"]["xInfContaTotal"] == 1 ? true : false,
            'xListaVenda' => isset($get["TransactionReport"]["xListaVendas"]) && $get["TransactionReport"]["xListaVendas"] == 1? true : false,
            'xImpressDeposit' => isset($get["TransactionReport"]["xImpressDeposit"]) && $get["TransactionReport"]["xImpressDeposit"] == 1? true : false,
            'xShowCabecalho' => isset($get["TransactionReport"]["xShowCabecalho"]) && $get["TransactionReport"]["xShowCabecalho"] == 1? true : false,
            'rowspan' => isset($get["TransactionReport"]["xInfContaTotal"]) && $get["TransactionReport"]["xInfContaTotal"] == 1 ? "4" : "3",
            'dataInicio' => $startDate,
            'dataFinal' => $endDate,
            'ordem' => $ordem
        ]);

        $pdf = Yii::$app->pdf;
        $pdf->filename = Yii::t('app', 'Consumers Extract') . ' - TBest.pdf';
        $pdf->options = ['title'=> Yii::t('app', 'Consumers Extract') . ' - TBest'];
        $pdf->content = $content;
        $pdf->methods = [
            'SetHeader'=>[
                '<div style="text-align: left;">
                    <img width="25px" src="' . Url::base('http') . '/images/newlogotipo-tbest-login.png" alt="Tbest">' . 'TBest - ' . Yii::t('app', 'Smart Consumption') .
                '</div>'
            ],
            'SetFooter'=>['{PAGENO}']
        ];
        
        return $pdf->render();
    }

    public function actionExtract(){
        $model = new TransactionReport;
        $model->minimovalor = "R$ 2,20";

        list($ano, $mes) = explode("-", date("Y-m"));
        $lastDay = date("t", mktime(0,0,0,$mes,'01',$ano));

        $model->inicio_periodo = \Date($ano.'-'.$mes.'-01');
        $model->fim_periodo = \Date($ano.'-'.$mes.'-'.$lastDay);

        return $this->render('extract',[
            'model' => $model
        ]);
    }

    public function actionRepresentativeComissionExport()
    {
        $model = new RepresentativeComission;
        $model->period = date('Y-m');
        $model->load($_GET);
        $model->user = \Yii::$app->user->getIdentity();
        list($model->year, $model->month) = explode("-", $model->period);

        if (Yii::$app->request->get() && isset(Yii::$app->request->get('RepresentativeComission')['consumer_representative_id'])){
            $modelconsumer = $this->findModel(Yii::$app->request->get('RepresentativeComission')['consumer_representative_id']);
            $legalPerson = $modelconsumer->legalPerson;
        }

        $dataProvider = $model->getTransactionReport();

        $dataProvider->pagination  = false;
        $dataProvider->sort = false;

        $content = $this->renderPartial('representative-comission-export', [
            'model' => $model,
            'consumer' => $modelconsumer,
            'dataProvider' => $dataProvider,
            'legalPerson' => isset($legalPerson)? $legalPerson : null,
            'generateCsv' => Yii::$app->request->get('export')
        ]);

        $pdf = Yii::$app->pdf;
        $pdf->filename = Yii::t('app', 'Representative Comission Relart') . ' - TBest.pdf';
        $pdf->options = ['title'=> Yii::t('app', 'Representative Comission Relart') . ' - TBest'];
        $pdf->content = $content;
        $pdf->methods = [
            'SetHeader'=>[
                '<div style="text-align: left;">
                    <img width="25px" src="' . Url::base('http') . '/images/newlogotipo-tbest-login.png" alt="Tbest">' . 'TBest - ' . Yii::t('app', 'Smart Consumption') .
                '</div>'
            ],
            'SetFooter'=>['{PAGENO}']
        ];

        return $pdf->render();
    }

    public function actionRepresentativeComission()
    {
        $model = new RepresentativeComission;
        $model->period = date('Y-m');
        $model->load($_GET);
        $model->user = \Yii::$app->user->getIdentity();
        list($model->year, $model->month) = explode("-", $model->period);

        return $this->render('representative-comission', [
            'modelRepresentativeComission' => $model,
            'generateCsv' => Yii::$app->request->get('export')
        ]);
    }

    public function actionRepresentativeReport()
    {
        $model = new RepresentativeReportSearch;
        $model->period = date('Y-m');

        $model->load($_GET);

        $model->user = \Yii::$app->user->getIdentity();

        list($model->year, $model->month) = explode("-", $model->period);

        $dataProvider = $model->search(Yii::$app->request->queryParams);

        return $this->render('representative-report', [
            'modelRepresentativeReport' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionRepresentativeReportExport()
    {
        $model = new RepresentativeReportSearch;
        $model->period = date('Y-m');

        $model->load($_GET);

        $model->user = \Yii::$app->user->getIdentity();

        list($model->year, $model->month) = explode("-", $model->period);

        $dataProvider = $model->search(Yii::$app->request->queryParams);
        $dataProvider->pagination  = false;
        $dataProvider->sort = false;
        
        $content = $this->renderPartial('representative-report-export', [
            'modelRepresentativeReport' => $model,
            'dataProvider' => $dataProvider
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

    public function actionNetworkReport()
    {
        $model = new NetworkReportSearch;
        $model->period = date('Y-m');

        $model->load($_GET);

        $model->user = \Yii::$app->user->getIdentity();

        list($model->year, $model->month) = explode("-", $model->period);

        $dataProvider = $model->search(Yii::$app->request->queryParams);

        return $this->render('network-report', [
            'modelNetReport' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionInformations($id)
    {
        return $this->renderPartial('informations', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionPoints($id)
    {
        //die(var_dump($config));
        $this->layout = 'graph-points';
        return $this->render('points', [
            'model' => $this->findModel($id),

        ]);
    }

    public function actionCadastroFora($identificador = 0){
        $consumer = new Consumer();
        $physicalPerson = new PhysicalPerson();
        $legalPerson = new LegalPerson();
        if($identificador <= 0){
            $consumidor = $consumer::find()->Where(['identifier'=>'551'])->one();
        }else{
            $consumidor = $consumer::find()->Where(['identifier'=>$identificador])->one();
        }
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if ($consumer->load($post) & $legalPerson->load($post) & $physicalPerson->load($post)) {
                $transaction = Yii::$app->db->beginTransaction();
                if ($physicalPersonExistente = PhysicalPerson::findOne(['cpf' => $physicalPerson->cpf])) {
                    $physicalPerson = $physicalPersonExistente;
                    $legalPerson = LegalPerson::findOne(['person_class' => 'PhysicalPerson', 'person_id' => $physicalPersonExistente->id]);
                    $legalPerson->load($post);  
                    $physicalPerson->load($post);
                }
                $physicalPerson->born_on = substr($post['PhysicalPerson']['born_on'], 6, 4)."-".substr($post['PhysicalPerson']['born_on'], 3, 2)."-".substr($post['PhysicalPerson']['born_on'], 0, 2);
                if($post['PhysicalPerson']['partner_born_on'] != ""){
                    $physicalPerson->partner_born_on = substr($post['PhysicalPerson']['partner_born_on'], 6, 4)."-".substr($post['PhysicalPerson']['partner_born_on'], 3, 2)."-".substr($post['PhysicalPerson']['partner_born_on'], 0, 2);
                }

                if ($physicalPerson->save()) {
                    $legalPerson->person_class = 'PhysicalPerson';
                    $legalPerson->person_id = $physicalPerson->id;

                    if ($legalPerson->validate() && $legalPerson->save()) {
                        $consumer->legal_person_id = $legalPerson->id;
                        $consumer->plane_id = 1;
                        $resultQualification = $consumidor->UltimoConsumersNetWork();
                        $quantidadeQualification = ($resultQualification[count($resultQualification)-1]);
                        $parente = new Consumer();
                        $parente = $parente->find()->Where(['id'=>$quantidadeQualification])->orderBy(['created_at'=>SORT_DESC])->one();
                        $filhos = $parente->getChildrenConsumers()->all();
                        
                        if(count($filhos) <= 0){
                            $consumer->position = "left";
                        }else if(count($filhos) == 1){
                            $consumer->position = "right";
                        }

                        $consumer->parent_consumer_id = $parente->id;
                        $consumer->sponsor_consumer_id = $consumidor->id;
                        
                        if ($consumer->save()) {
                            $transaction->commit();
                            //$consumer->removeDad();
                            Yii::$app->session->setFlash('success', Yii::t('app', 'Consumer successfully created.'));
                            return $this->redirect(['consumers/pagamento', 'id' => $consumer->id]);
                        }else{
                            $physicalPerson->isNewRecord = true;
                            foreach ($consumer->errors as $key => $value) {
                                if(strlen($value[0]) > 0){
                                    Yii::$app->session->setFlash('error', Yii::t('app', $value[0]));
                                }
                            }
                        }
                    }else{
                        $physicalPerson->isNewRecord = true;
                        foreach ($legalPerson->errors as $key => $value) {
                            if(strlen($value[0]) > 0){
                                Yii::$app->session->setFlash('error', Yii::t('app', $value[0]));
                            }
                        }
                    }
                }else{
                    $physicalPerson->isNewRecord = true;
                    foreach ($physicalPerson->errors as $key => $value) {
                        if(strlen($value[0]) > 0){
                            Yii::$app->session->setFlash('error', Yii::t('app', $value[0]));
                        }
                    }
                }

                $transaction->rollBack();
            }
        }
        
        if($physicalPerson->born_on != null){
            $physicalPerson->born_on = \Yii::$app->formatter->asDate($physicalPerson->born_on);
        }
        
        if($physicalPerson->partner_born_on != ""){
            $physicalPerson->partner_born_on = \Yii::$app->formatter->asDate($physicalPerson->partner_born_on);
        }
        
        return $this->render('cadastro-fora', [
            'physicalPerson' => $physicalPerson,
            'legalPerson' => $legalPerson,
            'consumer' => $consumer,
        ]);
    }

    public function actionPagamento($id){
        
        $consumer = Consumer::find()->Where(['id' => $id])->one();
        $faturamento = new Faturamento();

        $parcelas = array();

        $plane = Plane::find()->all();

        $parcelaMaximo = Configuration::getConfigurationValue(Configuration::ID_QUANTIDADE_MAXIMO_PARCELAS);

        foreach($plane as $item){
            $parcelas[$item->id] = [
                'plano' => $item->name_plane,
                'valor' => $item->value,
                'parcelas' => $this->getParcelas($item->getParcelas()),
            ];
        }

        return $this->render('pagamento', [
            'codigo' => $id,
            'faturamento' => $faturamento,
            'consumer' => $consumer,
            'parcelas' => $parcelas,
        ]);
    }

    public function getParcelas($listParcelas){
        $parcelas = [];
        
        foreach($listParcelas as $item){
            if(strpos($item["parcela"], "Investimento-") === false){
                if(strpos($item["parcela"], "Anos") === false){
                    $parcelas[] = [
                        'numero' => $item["parcela"],
                        'valor' => $item["valor"],
                        'valorParcela' => (($item["parcela"] == "00")? 'Á vista' : 'Parcela '.($item["parcela"])).' - '.$item["valor"]
                    ];
                }else{
                    $parcelas[] = [
                        'numero' => $item["parcela"]*12,
                        'valor' => $item["valor"],
                        'valorParcela' => $item["parcela"].' - '.$item["valor"]
                    ];
                }
            }else{
                $parcelas[] = [
                    'numero' => "nenhum",
                    'valor' => 0,
                    'valorParcela' => $item["parcela"]
                ];
            }
        }
        return $parcelas;
    }

    public function actionProcessarPagamento(){

        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
        }
        else{
            Yii::$app->session->setFlash('error', Yii::t('app', 'Envio de dados incorreto!'));
            return $this->redirect(['consumers/pagamento', 'id' => Yii::$app->request->post['Faturamento']['consumer_id']]);
        }

        $consumer = Consumer::find()->Where(['id' => $post['Faturamento']['consumer_id']])->one();
        
        if($consumer->identifier == null || $consumer->identifier == ""){
            $consumer->identifier = $consumer->getNextFreeIdentifier();
            $consumer->plane_id = $post['Faturamento']['plane_id'];
            if ($consumer->atualizarIdentifierPlano()) {
                $transaction = Yii::$app->db->beginTransaction();
                if($consumer->activateUser()){
                    $consumer->trigger(Consumer::EVENT_SET_REPRESENTATIVE_PERMISSION);
                    $transaction->commit();
                    return $this->redirect("https://loja.jedax.com.br/index.php?route=account/acessotbest&username=".$consumer->identifier."&token=".password_hash($consumer->identifier."tbestsistema", PASSWORD_BCRYPT));
                }
            }else{
                Yii::$app->session->setFlash('error', Yii::t('app', 'Erro ao ativar o usuario, por favor contacte o suporte!'));
                return $this->redirect(['consumers/pagamento', 'id' => $consumer->id]);
            }
        }else{
            return $this->redirect("https://loja.jedax.com.br/index.php?route=account/acessotbest&username=".$consumer->identifier."&token=".password_hash($consumer->identifier."tbestsistema", PASSWORD_BCRYPT));
        }
    }

    public function createPagamento($plane, $consumer, $parcelas, $urlretorno){
        
        $parcelaMaximo = Configuration::getConfigurationValue(Configuration::ID_QUANTIDADE_MAXIMO_PARCELAS);
        $pagamentoApi = new Payments();
        $pagamentoApi->customer = $consumer->id_asaas;
        $pagamentoApi->value = $parcelas <= $parcelaMaximo ? $plane->value : $plane->calculateProfitInvestiment($parcelas);
        $error = 0;
        if($parcelas > 1 && $parcelas < $parcelaMaximo){
            $pagamentoApi->billingType = "CREDIT_CARD";
        }
        $pagamentoApi->installmentCount = $parcelas > $parcelaMaximo ? 1 : $parcelas;
        $pagamentoApi->description = "Pagamento do Cadastro do Cliente ".$consumer->legalPerson->name;
        $pagamentoApi->externalReference = $consumer->id;
        $retornoPagamento = $pagamentoApi->CadastrarPagamento();
        $dadosRetorno = json_decode($retornoPagamento->response);
        if(isset($dadosRetorno->netValue)){
            $faturamento = new Faturamento();
            $faturamento->prepareSave($consumer, $plane->value, $dadosRetorno);
            if($faturamento->save()){
                $contas = new ContasReceber();
                $contas->prepareSave($faturamento, $consumer, $dadosRetorno->invoiceNumber, $parcelas);
                if($contas->save()){
                    $contaReceberBling = new ContaReceberBling();
                    $contaReceberBling->generateXml($consumer, $contas, "Conta a Pagar Cadastro Cliente", $plane);
                    $retornoConta = $contaReceberBling->CadastrarContaReceber();
                    for($i = 1; $i <= ($parcelas > $parcelaMaximo ? 1 : $parcelas); $i++){
                        $contaParcela = new ContasReceberParcelas();
                        $contaParcela->vencimento = date('Y-m-d H:i:s', strtotime('+'.($i-1).' month'));
                        $contaParcela->prepareSave($contas, $i);
                        if(!$contaParcela->save())
                        {
                            $error = 1;
                            Yii::$app->session->setFlash('error', Yii::t('app', 'Sua cobrança foi criada, verifique seu e-mail.'));
                        }
                    }
                }else{
                    $error = 1;
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Sua cobrança foi criada, verifique seu e-mail.'));
                }
            }else{
                $error = 1;
                Yii::$app->session->setFlash('error', Yii::t('app', 'Aconteceu algum erro na criação do faturamento, porfavor contacte o administrativo.'));
            }
        }else{
            $error = 1;
            Yii::$app->session->setFlash('error', Yii::t('app', 'Aconteceu algum erro na criação do pagamento, porfavor contacte o administrativo.'));
        }

        if($error){
            Yii::$app->mailer->compose(
                'layouts/html',
                [
                    'content' => print_r($retornoPagamento),
                ]
            )
            ->setFrom(getenv('MAILER_FROM'))
            ->setTo('williansilva@unochapeco.edu.br')
            ->setSubject(Yii::t('app/mail', 'Erro in system create payments'))
            ->send();
            return $this->redirect($urlretorno);
        }else{
            if($parcelas > $parcelaMaximo){
                return $this->redirect(['consumers/gerar-contrato-investimento', 'id' => $consumer->id]);
            }
            return $this->redirect($dadosRetorno->invoiceUrl);
        }
    }

    public function actionGerarContratoInvestimento($id){

        $faturamento = new Faturamento();
        $faturamento = $faturamento->find()->Where(['object_id'=>$id])->andWhere(['person_class'=>'PhysicalPerson'])->orderBy(['created_at'=>SORT_DESC])->one();

        $investimento = new Investimento();
        $investimento = $investimento->find()->Where(['consumer_id'=>$id])->orderBy(['created_at'=>SORT_DESC])->one();
        $investimento->GerarContratoInvestimento(Pdf::DEST_FILE);
        $investimento->sendContratoInvestimento($faturamento->url_invoice);
        return $this->render('gerar-contrato-investimento', [
            'consumerId' => $id,
            'invoiceUrl' => ($faturamento) ? $faturamento->url_invoice : ""
        ]);
    }

    public function actionDownloadInvestimento($id){
        $investimento = new Investimento();
        $investimento = $investimento->find()->Where(['consumer_id'=>$id])->orderBy(['created_at'=>SORT_DESC])->one();

        $investimento->GerarContratoInvestimento();
    }

    public function actionProcessarContatoBling(){
        $consumer = Consumer::find()->Where('is_disabled != 1')->all();
        foreach($consumer as $cliente){
            $dadosapi = new ContatoBling();
            $dadosapi->prepararCliente($cliente);
        }

        Yii::$app->session->setFlash('success', Yii::t('app', 'Cadastro do bling feito!'));
        return $this->redirect(['site/index']);
    }

    public function actionProcessarConsumersInvestimento(){
        $consumer = Consumer::find()->Where('plane_investiment_id > 0')->all();
        foreach($consumer as $cliente){
            $dadosapi = new Costumers();
            if($cliente->id_asaas == null){
                $this->DadosParaAsaasCustomers($dadosapi, $cliente);
                $verifica = $dadosapi->FiltroBuscarCliente();
                $dadosRetorno = json_decode($verifica->response);
                if($dadosRetorno->totalCount > 0){
                    $dadosRetorno = $dadosRetorno->data[0];
                    $consumerbanco = Consumer::find()->Where(['id'=>$cliente->id])->one();
                    $consumerbanco->id_asaas = $dadosRetorno->id;
                    $consumerbanco->updateIdAsaas();
                }else{
                    $result = $dadosapi->CadastrarCliente();
                    $dadosRetorno = json_decode($result->response);
                }
            }
        }
    }

    public function actionCadastrarAsaas($id){
        $consumer = $this->findModel($id);
        if($consumer){
            $dadosapi = new Costumers();
            if($consumer->id_asaas == null){
                $this->DadosParaAsaasCustomers($dadosapi, $consumer);
                $verifica = $dadosapi->FiltroBuscarCliente();
                $dadosRetorno = json_decode($verifica->response);
                if($dadosRetorno->totalCount > 0){
                    $dadosRetorno = $dadosRetorno->data[0];
                    $consumerbanco = Consumer::find()->Where(['id'=>$consumer->id])->one();
                    $consumerbanco->id_asaas = $dadosRetorno->id;
                    $consumerbanco->updateIdAsaas();
                }else{
                    $result = $dadosapi->CadastrarCliente();
                    $dadosRetorno = json_decode($result->response);
                    $consumer->id_asaas = $dadosRetorno->id;
                    $consumer->updateIdAsaas();
                }
            }

            $dadosapibling = new ContatoBling();
            $dadosapibling->prepararCliente($consumer);

            return json_encode(array(
                'success'=> true,
                'mensagem' => "Consumidor Cadastrado Corretamente Asaas"
            ));
        }else{
            return json_encode(array(
                'success'=> false,
                'mensagem' => "Erro ao encontrar o consumidor"
            ));
        }
    }

    public function actionTermos()
    {
        $contrato = new Contratos();
        
        return $contrato->buscarTermosCadastro();
    }

    private function DadosParaAsaasCustomers($dadosapi, $consumer){
        $dadosapi->name = $consumer->legalPerson->name;
        $dadosapi->email = $consumer->legalPerson->email;
        $dadosapi->phone = preg_replace("/[^0-9]/", "", $consumer->legalPerson->home_phone);
        $dadosapi->mobilePhone = preg_replace("/[^0-9]/", "", $consumer->legalPerson->phoneNumber);
        $dadosapi->cpfCnpj = preg_replace("/[^0-9]/", "", $consumer->legalPerson->person->cpf);
        $dadosapi->postalCode = preg_replace("/[^0-9]/", "", $consumer->legalPerson->zip_code);
        $dadosapi->address = $consumer->legalPerson->address;
        $dadosapi->addressNumber = preg_replace("/[^0-9]/", "", $consumer->legalPerson->address);
        $dadosapi->complement = $consumer->legalPerson->address_complement;
        $dadosapi->province = $consumer->legalPerson->district;
        $dadosapi->externalReference = $consumer->identifier;
    }

    /**
     * Finds the Consumer model based on its primary key value.
     * If the model is not found, a 404 HTTP exzip_codetion will be thrown.
     * @param integer $id
     * @return Consumer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Consumer::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
