<?php
namespace app\api\controllers;

use Yii;
use yii\web\Controller;
use app\models\Faturamento;

class FaturamentoController extends Controller
{
    public function beforeAction($action) 
    { 
        $this->enableCsrfValidation = false; 
        return parent::beforeAction($action); 
    }

    public function actionAsaas(){
        $dados = json_decode(\Yii::$app->request->getRawBody());
        if($dados->event == "PAYMENT_RECEIVED"){
            $faturamento = new Faturamento();
            $retornoFat = $faturamento->find()->Where(['payment_id'=>$dados->payment->id])->one();
            

            echo '<pre>';
            print_r($retornoFat);
            echo '</pre>';
            exit();
        }
    }
}