<?php

namespace app\controllers;

use Yii;
use yii\helpers\Url;
use app\models\Offer;
use yii\web\Controller;
use app\models\Business;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use app\models\search\OfferSearch;

class OfferController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['uploadimagem'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions'=> ['index', 'create', 'uploadimagem'],
                        'roles' => ['consumer', 'receptionist', 'salesReport']
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

    public function beforeAction($action)
    {            
        if ($action->id == 'uploadimagem') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    public function actionIndex(){

        $searchModel = new OfferSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate(){
        $offer = new Offer();
        $post = Yii::$app->request->post();

        if(Yii::$app->request->isPost){
            $post['Offer']['dt_inicial'] = (strpos($post['Offer']['dt_inicial'], "__:__:__") > 0) ? str_replace([" __:__:__", "/"], ["","-"], $post['Offer']['dt_inicial']) : str_replace("/","-",$post['Offer']['dt_inicial']);
            $post['Offer']['dt_final'] = (strpos($post['Offer']['dt_final'], "__:__:__") > 0) ? str_replace([" __:__:__", "/"], ["","-"], $post['Offer']['dt_final']) : str_replace("/","-",$post['Offer']['dt_final']);
            $dt_inicial = new \Datetime($post['Offer']['dt_inicial'].((strpos($post['Offer']['dt_inicial'], ":") === false) ? " 00:00:00" : ""));
            $dt_final = new \Datetime($post['Offer']['dt_final'].((strpos($post['Offer']['dt_inicial'], ":") === false) ? " 23:59:59" : ""));
            $post['Offer']['dt_inicial'] = $dt_inicial->format('Y-m-d H:i:s');
            $post['Offer']['dt_final'] = $dt_final->format('Y-m-d H:i:s');
            $offer->usuario_id = Yii::$app->user->id;
            if($post['image']){
                $offer->image = trim($post['image']);
            }
            if ($offer->load($post) && $offer->validate() && $offer->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Offer successfully created.'));
                return $this->redirect(['index']);
            }else if($offer->errors){
                print_r($offer->errors);
                exit();
            }
        }

        $data = new \DateTime();

        $offer->dt_inicial = $data->format('d/m/Y H:i:s');
        $offer->dt_final = $data->format('d/m/Y H:i:s');

        if (!Yii::$app->user->can('admin')) {
            $offer->convenio_id = Yii::$app->user->identity->authenticable_id;
        }
        
        return $this->render('create', ['offer'=>$offer]);
    }

    public function actionUpdate($id){
        $offer = $this->findModel($id);

        if (Yii::$app->request->isPost){
            $post = Yii::$app->request->post();

            $post['Offer']['dt_inicial'] = (strpos($post['Offer']['dt_inicial'], "__:__:__") > 0) ? str_replace([" __:__:__", "/"], ["","-"], $post['Offer']['dt_inicial']) : str_replace("/","-",$post['Offer']['dt_inicial']);
            $post['Offer']['dt_final'] = (strpos($post['Offer']['dt_final'], "__:__:__") > 0) ? str_replace([" __:__:__", "/"], ["","-"], $post['Offer']['dt_final']) : str_replace("/","-",$post['Offer']['dt_final']);
            $dt_inicial = new \Datetime($post['Offer']['dt_inicial'].((strpos($post['Offer']['dt_inicial'], ":") === false) ? " 00:00:00" : ""));
            $dt_final = new \Datetime($post['Offer']['dt_final'].((strpos($post['Offer']['dt_inicial'], ":") === false) ? " 23:59:59" : ""));
            $post['Offer']['dt_inicial'] = $dt_inicial->format('Y-m-d H:i:s');
            $post['Offer']['dt_final'] = $dt_final->format('Y-m-d H:i:s');

            $offer->usuario_id = Yii::$app->user->id;

            if($post['image'] != ""){
                if(trim($post['image']) != trim($offer->image) && file_exists("../web/".trim($offer->image))){
                    unlink("../web/".trim($offer->image));
                }
                $offer->image = $post['image'];
            }
            if($offer->load($post) && $offer->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Offer successfully updated.'));
                return $this->redirect(['view', 'id' => $offer->id]);
            }
        }
        $dt_inicial = new \DateTime($offer->dt_inicial);
        $dt_final = new \DateTime($offer->dt_final);

        $offer->dt_inicial = $dt_inicial->format('d/m/Y H:i:s');
        $offer->dt_final = $dt_final->format('d/m/Y H:i:s');
        
        return $this->render('update', ['offer'=>$offer]);
    }

    public function actionView($id, $tab = "general"){
        return $this->render('view', [
            'offer' => $this->findModel($id),
            'tab' =>  $tab,
        ]);
    }

    public function actionDelete($id)
    {
        $offer = $this->findModel($id);
        if($offer->image){
            unlink("../web/".trim($offer->image));
        }
        if($offer->delete()){
            Yii::$app->session->setFlash('success', Yii::t('app', 'Plane successfully deleted.'));
            return $this->redirect(['index']);
        }
    }

    public function actionUploadimagem(){
        $errors = [];
        $comAcentos = array(' ','à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');

        $semAcentos = array('', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', '0', 'U', 'U', 'U');
        
        if(!Yii::$app->user->can('admin') && Yii::$app->user->can('salesReport')){
            $business = Business::findOne(Yii::$app->user->identity->authenticable_id);
        }
        $post = Yii::$app->request->post();
        $data = new \DateTime();
        $uploaddir = '../web/uploads/offer/'.((isset($business)) ? str_replace($comAcentos, $semAcentos, strtolower($business->legalPerson->name)) : "oferta" );
        if(!is_dir($uploaddir))
            mkdir($uploaddir);

        $uploadfile = $uploaddir . "/oferta".$data->format('His').".".pathinfo($_FILES['imagemoferta']['name'], PATHINFO_EXTENSION);
        if(file_exists($uploadfile))
            unlink($uploadfile);

        if (move_uploaded_file($_FILES['imagemoferta']['tmp_name'], $uploadfile)) {
            if($post['antigo'] != ""){
                unlink("../web/".trim($post['antigo']));
            }
            $success = true;
            $link = str_replace("../web/", "", $uploadfile);
            $msg = "O arquivo é valido e foi carregado com sucesso.\n";
        } else {
            $success = false;
            $link = "";
            $msg = "Algo está errado aqui!\n";
        }

        return json_encode([
            'success'=> $success,
            'mensagem' => $msg,
            'link' => $link,
            'errors' => $errors,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Offer::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
