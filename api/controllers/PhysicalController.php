<?php 

namespace app\api\controllers; // Define o namespace do controlador, que ajuda a organizar o código e evitar conflitos de nomes

use Yii; // Importa a classe Yii, que é a principal classe da estrutura Yii e oferece várias funcionalidades
use yii\helpers\ArrayHelper; // Importa a classe ArrayHelper, que fornece métodos utilitários para trabalhar com arrays
use app\models\PhysicalPerson; // Importa a classe PhysicalPerson, que representa o modelo de pessoa física
use app\models\search\ConsumerSearch; // Importa a classe ConsumerSearch que provavelmente contém a lógica de busca para consumidores
use app\models\LegalPerson; // Importa a classe LegalPerson, que representa o modelo de pessoa jurídica
use app\models\Consumable; // Importa a classe Consumable, que representa o modelo de consumíveis
use app\models\Consumer; // Importa a classe Consumer, que representa o modelo de consumidores
use app\models\City; // Importa a classe City, que representa o modelo de cidades
use app\models\Sale; // Importa a classe Sale, que representa o modelo de vendas
use app\models\User; // Importa a classe User, que representa o modelo de usuários
use yii\web\Controller; // Importa a classe Controller que fornece uma implementação base para controladores web
use yii\web\ForbiddenHttpException; // Importa a classe ForbiddenHttpException para lançar exceções HTTP 403

class PhysicalController extends Controller {

    // Ação padrão que renderiza a visão 'index'
    public function actionIndex(){
        return $this->render('index');
    }

    // Ação que busca consumidor pelo documento
    public function actionDocumentoConsumer($documento = "") {
        if (strlen($documento) > 0) {
            // Procura um consumidor pelo identificador (documento)
            $model = Consumer::findOne(['identifier' => $documento]);
    
            if (!$model) {
                // Lança uma exceção se o consumidor não for encontrado
                throw new ForbiddenHttpException("Documento não encontrado", 404);
            }
    
            // Procura a pessoa jurídica associada ao consumidor, se for uma pessoa física
            $legalPersonModel = LegalPerson::findOne(['id' => $model->legal_person_id, 'person_class' => 'PhysicalPerson']);
            $consumerSearch = $model;
            $dadosLegal = null;
            $dadosPhysical = null;
            $dadosCity = null;
    
            if ($legalPersonModel) {
                // Procura a cidade e a pessoa física associadas
                $dadosCity = City::findOne(['id' => $legalPersonModel->city_id]);
                $dadosPhysical = PhysicalPerson::findOne(['id' => $legalPersonModel->person_id]);
            }
    
            // Prepara os dados para retorno
            $dadosretorno = [
                'users' => User::findOne(['login' => $documento]),
                'consumidor' => $consumerSearch,
                'legalperson' => $dadosLegal,
                'physicalperson' => $dadosPhysical,
                'city' => $dadosCity
            ];
    
            return $dadosretorno;
        } else {
            // Lança uma exceção se o documento não for fornecido
            throw new ForbiddenHttpException("Documento obrigatório", 400);
        }
    }

    // Ação que registra uma venda
    public function actionRegistraVenda(){
        $documento = Yii::$app->request->queryParams['username'];

        $modelConsumer = new Consumer;
        $consultaSale = new Sale();
        $dadosConsumer = $modelConsumer->find()->where(['identifier' => $documento])->one();

        $invoiceitem = $consultaSale->find()->where(['invoice_code' => Yii::$app->request->queryParams['invoice']])->one();
        
        $model = new Sale();
        $model->invoice_code = ($invoiceitem ? "loja_" : "") . Yii::$app->request->queryParams['invoice'];
        $model->total = (float)substr(Yii::$app->request->queryParams['valor'], 0, -2) . "." . substr(Yii::$app->request->queryParams['valor'], -2);
        $model->consumable_id = Yii::$app->request->queryParams['repasse'] ? Yii::$app->request->queryParams['repasse'] : 1;
        $model->business_id = Yii::$app->request->queryParams['convenio'] ? Yii::$app->request->queryParams['convenio'] : 1;
        $model->consumer_id = $dadosConsumer->id;
        if ($model->save()) {
            return ["status" => "success"];
        } else {
            return ["status" => "failed", "invoice" => $model->invoice_code, "valor" => $model->total];
        }
    }

    // Ação que obtém pontos do mês atual
    public function actionGetPointsMonth(){
        return Yii::$app->user->getIdentity()->consumer->getMonthPoints(date('m'), date('Y'));
    }

    // Ação que obtém pontos do plano
    public function actionGetPointsPlane(){
        return Yii::$app->user->getIdentity()->consumer->plane->goal_points;
    }

    // Ação que obtém informações do consumidor pela porcentagem
    public function actionInfoConsumidorPorcent(){
        $modelConsumidor = new Consumer;
        if (isset(Yii::$app->request->queryParams['consumidor'])) {
            $dadosConsumidor = $modelConsumidor->find()->where(['identifier' => Yii::$app->request->queryParams['consumidor']])->one();
            $dadosConsumidor = $dadosConsumidor->plane->multiplier;
        } else {
            return "Obrigatório o id do Consumidor!";
        }

        return $dadosConsumidor;
    }

    // Ação que obtém informações do parceiro
    public function actionInfoParceiro(){
        $modelParceiro = new Consumable;
        if (isset(Yii::$app->request->queryParams['parceiro'])) {
            $dadosParceiro = $modelParceiro->find()->where(['description' => 'Venda Consumidor', 'business_id' => Yii::$app->request->queryParams['parceiro']])->one();
        } else {
            return "Obrigatório o id do Parceiro!";
        }

        return $dadosParceiro;
    }
}
