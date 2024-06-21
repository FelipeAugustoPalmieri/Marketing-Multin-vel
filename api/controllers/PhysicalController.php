<?php 

namespace app\api\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\PhysicalPerson;
use app\models\search\ConsumerSearch;
use app\models\LegalPerson;
use app\models\Consumable;
use app\models\Consumer;
use app\models\City;
use app\models\Sale;
use app\models\User;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;


class PhysicalController extends Controller{

	public function actionIndex(){
		return $this->render('index');
	}

	public function actionDocumentoConsumer($documento = "") {
		if (strlen($documento) > 0) {
			$model = Consumer::findOne(['identifier' => $documento]);
	
			if (!$model) {
				throw new ForbiddenHttpException("Documento não encontrado", 404);
			}
	
			$legalPersonModel = LegalPerson::findOne(['id' => $model->legal_person_id, 'person_class' => 'PhysicalPerson']);
			$consumerSearch = $model;
			$dadosLegal = null;
			$dadosPhysical = null;
			$dadosCity = null;
	
			if ($legalPersonModel) {
				$dadosCity = City::findOne(['id' => $legalPersonModel->city_id]);
				$dadosPhysical = PhysicalPerson::findOne(['id' => $legalPersonModel->person_id]);
			}
	
			$dadosretorno = [
				'users' => User::findOne(['login' => $documento]),
				'consumidor' => $consumerSearch,
				'legalperson' => $dadosLegal,
				'physicalperson' => $dadosPhysical,
				'city' => $dadosCity
			];
	
			return $dadosretorno;
		} else {
			throw new ForbiddenHttpException("Documento obrigatório", 400);
		}
	}

	public function actionRegistraVenda(){
		$documento = Yii::$app->request->queryParams['username'];

		$modelConsumer = new Consumer;
		$consultaSale = New Sale();
		$dadosConsumer = $modelConsumer->find()->where(['identifier'=>$documento])->one();

		$invoiceitem = $consultaSale->find()->where(['invoice_code'=>Yii::$app->request->queryParams['invoice']])->one();
		
		$model = new Sale();
		$model->invoice_code = ($invoiceitem ? "loja_": "") . Yii::$app->request->queryParams['invoice'];
		$model->total = (float)substr(Yii::$app->request->queryParams['valor'], 0, -2).".".substr(Yii::$app->request->queryParams['valor'], -2);
		$model->consumable_id = Yii::$app->request->queryParams['repasse'] ? Yii::$app->request->queryParams['repasse'] : 1;
		$model->business_id = Yii::$app->request->queryParams['convenio'] ? Yii::$app->request->queryParams['convenio'] : 1;
		$model->consumer_id = $dadosConsumer->id;
		if($model->save()){
			return array("status"=>"success");
		}else{
			return array("status"=>"falied", "invoice"=>$model->invoice_code, "valor"=>$model->total);
		}

	}

	public function actionGetPointsMonth(){
		return Yii::$app->user->getIdentity()->consumer->getMonthPoints(date('m'), date('Y'));
	}

	public function actionGetPointsPlane(){
		return Yii::$app->user->getIdentity()->consumer->plane->goal_points;
	}

	public function actionInfoConsumidorPorcent(){
		$modelConsumidor = new Consumer;
		if(isset(Yii::$app->request->queryParams['consumidor'])){
			$dadosConsumidor = $modelConsumidor->find()->where(['identifier'=>Yii::$app->request->queryParams['consumidor']])->one();
			$dadosConsumidor = $dadosConsumidor->plane->multiplier;
		}else{
			return "Obrigatório o id do Consumidor!";
		}

		return $dadosConsumidor;
	}

	public function actionInfoParceiro(){
		$modelParceiro = new Consumable;
		if(isset(Yii::$app->request->queryParams['parceiro'])){
			$dadosParceiro = $modelParceiro->find()->where(['description'=>'Venda Consumidor','business_id'=>Yii::$app->request->queryParams['parceiro']])->one();
		}else{
			return "Obrigatório o id do Parceiro!";
		}

		return $dadosParceiro;
	}
}