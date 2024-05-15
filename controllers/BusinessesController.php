<?php

namespace app\controllers;

use Exception;
use Yii;
use yii\helpers\Url;
use app\models\Business;
use app\models\LegalPerson;
use app\models\Consumer;
use app\models\PhysicalPerson;
use app\models\JuridicalPerson;
use app\models\search\BusinessSearch;
use app\models\search\BusinessReportSearch;
use app\models\search\ConsumableSearch;
use app\models\search\UserSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use kartik\mpdf\Pdf;
use app\models\Contratos;
use app\models\ListContracts;
use app\models\receitaws\ConsultaCnpj;

/**
 * BusinessesController implements the CRUD actions for Business model.
 */
class BusinessesController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['manageBusinesses'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['report', 'consumables-info', 'report-export'],
                        'roles' => ['viewBusinessesReport'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view', 'uploadimagem', 'consulta-cnpj', 'cadastro-complementar'],
                        'roles' => ['salesReport', 'admin', 'receptionist']
                    ],
                    [
                        'allow'=>true,
                        'actions' => ['cancel-contract', 'delete'],
                        'roles'=>['admin'],
                    ],
                    [
                        'allow' => true,
                        'actions'=> ['index', 'create', 'contract', 'consulta-cnpj', 'cadastro-complementar'],
                        'roles' => ['consumer', 'receptionist']
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
     * Lists all Business models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BusinessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $roleVerifier = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];

        if($roleVerifier != "admin" && !Consumer::find()->where(['id' => Yii::$app->user->identity->authenticable_id])->businessRepresentatives()->one() && !Yii::$app->user->can('receptionist')){
            $this->redirect(['site/index']);
        }

        if($roleVerifier != "admin" && (Consumer::find()->where(['id' => Yii::$app->user->identity->authenticable_id])->businessRepresentatives()->one()) && !Yii::$app->user->can('receptionist')){
            $this->redirect(['create']);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Business model.
     * @param integer $id
     * @param string $tab Open tab
     * @return mixed
     */
    public function actionView($id, $tab = null)
    {
        $usersSearchModel = new UserSearch();
        $usersSearchModel->authenticable_type = 'Business';
        $usersSearchModel->authenticable_id = $id;
        $usersDataProvider = $usersSearchModel->search(Yii::$app->request->queryParams);

        $consumableSearchModel = new ConsumableSearch();
        $consumableSearchModel->business_id = $id;
        $consumablesDataProvider = $consumableSearchModel->search(Yii::$app->request->queryParams);

        $dados = ListContracts::find()->Where(["object_type"=>"Business"])->andWhere(["object_id"=>$id])->orderBy(['created_at'=>SORT_DESC])->one();

        return $this->render('view', [
            'model' => $this->findModel($id),
            'usersDataProvider' => $usersDataProvider,
            'consumablesDataProvider' => $consumablesDataProvider,
            'listcontract'=> $dados,
            'ContractCancel' => ($dados && $dados->contractParent) ? $dados->contractParent : false,
            'tab' => $tab,
        ]);
    }

    /**
     * Creates a new Business model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $roleVerifier = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        if($roleVerifier != "admin" && !Consumer::find()->where(['id' => Yii::$app->user->identity->authenticable_id])->businessRepresentatives()->one() && !Yii::$app->user->can('receptionist')){
            $this->redirect(['site/index']);
        }
        $business = new Business(['scenario' => 'insert']);
        $legalPerson = new LegalPerson(['scenario' => 'insert']);
        $juridicalPerson = new JuridicalPerson();
        $physicalPerson = new PhysicalPerson();
        $post = Yii::$app->request->post();

        if(Yii::$app->request->isPost && $post["LegalPerson"]["person_class"] == 'PhysicalPerson'){
            
            $modelphysical = new PhysicalPerson;
            $physicaldados = $modelphysical->find();
            $physicaldados = $physicaldados->innerJoin("legal_persons as t","t.person_id = physical_persons.id")->where(['physical_persons.cpf' => $post["PhysicalPerson"]["cpf"], 't.person_class' => 'PhysicalPerson'])->one();
            

            if(count($physicaldados) > 0){
                $physicalPerson = $physicaldados;
                $modelLegalPerson = new LegalPerson;
                $legalPersondados = $modelLegalPerson->find()->where(['person_id'=>$physicalPerson->id, 'person_class' => 'PhysicalPerson'])->one();
                if(count($legalPersondados) > 0){
                    $legalPerson = $legalPersondados;
                }
            }
            
        }

        if ($legalPerson->load($post) & $business->load($post) & $juridicalPerson->load($post) & $physicalPerson->load($post)) {
            $transaction = Yii::$app->db->beginTransaction();
            
            if ($legalPerson->person_class == 'JuridicalPerson' && $juridicalPerson->save()) {
                $legalPerson->person_id = $juridicalPerson->id;
            } elseif ($legalPerson->person_class == 'PhysicalPerson' && $physicalPerson->save()) {
                $legalPerson->person_id = $physicalPerson->id;
            }
            
            if ($legalPerson->person_id && $legalPerson->save()) {
                
                $business->legal_person_id = $legalPerson->id;
                if ($business->save()) {
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Business successfully created.'));
                    return $this->redirect(['view', 'id' => $business->id]);
                }
            }

            $transaction->rollBack();
        }

        return $this->render('create', [
            'business' => $business,
            'legalPerson' => $legalPerson,
            'juridicalPerson' => $juridicalPerson,
            'physicalPerson' => $physicalPerson,
        ]);
    }

    /**
     * Updates an existing Business model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $business = $this->findModel($id);
        $business->setScenario('update');
        $legalPerson = $business->legalPerson;
        $legalPersonType = $legalPerson->person;
        $juridicalPerson = new JuridicalPerson();
        $physicalPerson = new PhysicalPerson();

        if ($legalPerson->person_class == 'JuridicalPerson') {
            $juridicalPerson = $legalPersonType;
        } elseif ($legalPerson->person_class == 'PhysicalPerson') {
            $physicalPerson = $legalPersonType;
        }
        
        $post = Yii::$app->request->post();
        
        $isDiferentClass = Yii::$app->request->isPost && $post['LegalPerson']['person_class'] != $legalPerson->person_class? true : false;
        $classPerson = Yii::$app->request->isPost ? $legalPerson->person_class : null;

        if ($business->load($post) & $legalPerson->load($post) & $legalPersonType->load($post)) {
            $transaction = Yii::$app->db->beginTransaction();
            $business->whatsapp = $post["Business"]["whatsapp"];
            $legalPerson->comercial_phone = $post["LegalPerson"]["comercial_phone"];
            if($isDiferentClass && $classPerson != null){
                if($classPerson == "JuridicalPerson"){
                    $auxjuridical = JuridicalPerson::findOne($juridicalPerson->id);
                    $auxjuridical->delete();
                    $physicalPerson->load($post);
                    $physicalPerson->save();
                    $legalPerson->person_id = $physicalPerson->id;
                }else if($classPerson == "PhysicalPerson"){
                    $auxphysical = PhysicalPerson::findOne($physicalPerson->id);
                    $auxphysical->delete();
                    $juridicalPerson->load($post);
                    $juridicalPerson->save();
                    $legalPerson->person_id = $juridicalPerson->id;
                }
            }

            if ($legalPerson->save() && $business->save() && $legalPersonType->save()) {
                $transaction->commit();
                Yii::$app->session->setFlash('success', Yii::t('app', 'Business successfully updated.'));
                return $this->redirect(['view', 'id' => $business->id]);
            }

            $transaction->rollBack();
        }

        return $this->render('update', [
            'business' => $business,
            'legalPerson' => $legalPerson,
            'juridicalPerson' => $juridicalPerson,
            'physicalPerson' => $physicalPerson,
        ]);
    }

    public function actionDelete($id){
        echo $id;
        
        exit();
    }

    public function actionContract($id)
    {
        $model = $this->findModel($id);

        if(!Yii::$app->user->can('admin') && !Consumer::find()->where(['id' => Yii::$app->user->identity->authenticable_id])->businessRepresentatives()->one()){
            Yii::$app->session->setFlash('error', Yii::t('app', 'dont have permission now'));
            return $this->redirect(['view', 'id' => $id]);
        }

        $listContratos = new ListContracts();

        $dados = $listContratos->find()->Where(["object_type"=>"Business"])->andWhere(["object_id"=>$model->id])->andWhere(["is_cancel"=>0])->one();

        if(!$dados){
            
            if(count($model->consumables) <= 0){
                Yii::$app->session->setFlash('error', Yii::t('app', 'Please enter pass.'));
                return $this->redirect(['view', 'id' => $id]);
            }

            $contrato = new Contratos();

            $findContrato = $contrato->find()->Where(['flag_local'=>3])->andWhere(['flag_cancel'=>0])->orderBy(['created_at'=>SORT_DESC])->one();

            if(!$model->representative_legal || !$model->representative_cpf){
                Yii::$app->session->setFlash('error', Yii::t('app', 'dot not in information representative legal'));
                return $this->redirect(['view', 'id' => $id]);
            }

            $dataContract = $model->getDataContract();

            $textoNovo = str_replace($dataContract['antes'], $dataContract['depois'], $findContrato->texto);

            $listContratos->object_id = $model->id;
            $listContratos->object_type = "Business";
            $listContratos->usuario_id = Yii::$app->user->id;
            $listContratos->contract = $textoNovo;
            $listContratos->contract_id = $findContrato->id;
            $listContratos->save();
        }else{
            $textoNovo = $dados->contract;
        }

        $pdf = Yii::$app->pdf;
        $pdf->filename = Yii::t('app', 'Contract the bonus transfer') . ' - TBest.pdf';
        $pdf->content = $textoNovo;
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

    public function actionCancelContract($id)
    {
        $listContratos = new ListContracts();
        $contrato = new Contratos();

        $dados = $listContratos->find()->Where(["object_type"=>"Business"])->andWhere(["object_id"=>$id])->andWhere(["is_cancel"=>0])->one();

        if($dados){
            $model = $this->findModel($id);
            $findContrato = $contrato->find()->Where(['flag_local'=>3])->andWhere(['flag_cancel'=>1])->orderBy(['created_at'=>SORT_DESC])->one();

            $dataContract = $model->getDataContract();

            $dados->CancelContract();

            $textoNovo = str_replace($dataContract['antes'], $dataContract['depois'], $findContrato->texto);

            $ContractCancel = new ListContracts;

            $ContractCancel->object_id = $dados->object_id;
            $ContractCancel->usuario_id = $dados->usuario_id;
            $ContractCancel->object_type = "Business";
            $ContractCancel->contract = $textoNovo;
            $ContractCancel->contract_id = $findContrato->id;
            $ContractCancel->is_cancel = 1;

            $ContractCancel->save();

            $pdf = Yii::$app->pdf;
            $pdf->filename = Yii::t('app', 'Cancel Contract the bonus transfer') . ' - TBest.pdf';
            $pdf->content = $textoNovo;
            $pdf->methods = [
                'SetHeader'=>[
                    '<div style="text-align: left">
                        <img width="25px" src="https://sistema.tbest.com.br/images/newlogotipo-tbest-login.png" alt="Tbest">' . 'TBest - ' . Yii::t('app', 'Smart Consumption') .
                    '</div>'
                ],
                'SetFooter'=>['{PAGENO}']
            ];

            return $pdf->render();
            
        }else{
            Yii::$app->session->setFlash('error', Yii::t('app', 'error in cancel contract'));
            $firstRole = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];

            if ($firstRole == 'business') {
                return $this->redirect(['sales/create']);
            }else{
                return $this->redirect(['site']);
            }
        }
    }

    public function actionReport()
    {
        $searchModel = new BusinessReportSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('report', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionReportExport()
    {
        $searchModel = new BusinessReportSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination  = false;

        $content = $this->renderPartial('report-export', [
            'dataProvider' => $dataProvider,
        ]);

        $pdf = Yii::$app->pdf;
        $pdf->filename = Yii::t('app', 'Businesses Report') . ' - TBest.pdf';
        $pdf->content = $content;
        $pdf->methods = [
            'SetHeader'=>[
                '<div style="text-align: left">
                    <img width="25px" src="' . Url::base('http') . '/images/newlogotipo-tbest-login.png" alt="Tbest">' . 'TBest - ' . Yii::t('app', 'Smart Consumption') .
                '</div>'
            ],
            'SetFooter'=>['{PAGENO}']
        ];

        return $pdf->render(); 
    }

    public function actionConsumablesInfo($id)
    {
        $consumableSearchModel = new ConsumableSearch();
        $consumableSearchModel->business_id = $id;
        $consumablesDataProvider = $consumableSearchModel->search(Yii::$app->request->queryParams);

        return $this->renderPartial('consumables-info', [
            'model' => $this->findModel($id),
            'consumablesDataProvider' => $consumablesDataProvider,
        ]);
    }

    public function actionUploadimagem(){
        $errors = [];
        $comAcentos = array(' ','à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');

        $semAcentos = array('', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', '0', 'U', 'U', 'U');

        $business = $this->findModel(Yii::$app->user->identity->authenticable_id);

        $uploaddir = '../web/uploads/business/'.str_replace($comAcentos, $semAcentos, strtolower($business->legalPerson->name));
        if(!is_dir($uploaddir))
            mkdir($uploaddir);

        $uploadfile = $uploaddir . "/logoempresa.".pathinfo($_FILES['logoempresa']['name'], PATHINFO_EXTENSION);
        if(file_exists($uploadfile))
            unlink($uploadfile);

        if (move_uploaded_file($_FILES['logoempresa']['tmp_name'], $uploadfile)) {
            $success = true;
            $msg = "O arquivo é valido e foi carregado com sucesso.\n";

            $business->logoempresa = str_replace("../web/", "", $uploadfile);

            $business->updateImagem();
        } else {
            $success = false;
            $msg = "Algo está errado aqui!\n";
        }

        return json_encode(array(
            'success'=> $success,
            'mensagem' => $msg,
            'errors' => $errors,
        ));
    }

    public function actionConsultaCnpj(){
        $modelconsulta = new ConsultaCnpj();
        $post = Yii::$app->request->post();
        
        $dados = $modelconsulta->ConsultaCnpj(preg_replace("/[^0-9]/", "", $post['cnpj']));
        
        return $dados->response;
    }

    public function actionCadastroComplementar(){
        $post = Yii::$app->request->post();

        $business = $this->findModel(Yii::$app->user->identity->authenticable_id);
        $business->legalPerson->comercial_phone = $post["comercial"];
        $business->whatsapp = $post["whats"];

        $business->updateComplementos();

        return json_encode(["success"=>true]);
    }

    /**
     * Finds the Business model based on its primary key value.
     * If the model is not found, a 404 HTTP exzip_codetion will be thrown.
     * @param integer $id
     * @return Business the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Business::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}