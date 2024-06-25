<?php
namespace app\api\controllers; // Define o namespace do controlador, que ajuda a organizar o código e evitar conflitos de nomes

use Yii; // Importa a classe Yii, que é a principal classe da estrutura Yii e oferece várias funcionalidades
use yii\web\Controller; // Importa a classe Controller que fornece uma implementação base para controladores web
use app\models\Faturamento; // Importa a classe Faturamento, que representa o modelo de faturamento

class FaturamentoController extends Controller
{
    // Método que é executado antes de qualquer ação
    public function beforeAction($action) 
    { 
        $this->enableCsrfValidation = false; // Desabilita a validação CSRF para este controlador
        return parent::beforeAction($action); // Chama o método beforeAction da classe pai
    }

    // Ação personalizada chamada 'asaas'
    public function actionAsaas(){
        // Obtém o corpo bruto da requisição e decodifica de JSON para objeto PHP
        $dados = json_decode(\Yii::$app->request->getRawBody());
        
        // Verifica se o evento recebido é 'PAYMENT_RECEIVED'
        if($dados->event == "PAYMENT_RECEIVED"){
            // Cria uma nova instância do modelo Faturamento
            $faturamento = new Faturamento();
            // Busca um registro de faturamento com o payment_id correspondente
            $retornoFat = $faturamento->find()->where(['payment_id' => $dados->payment->id])->one();
            
            // Exibe os dados do faturamento encontrado
            echo '<pre>';
            print_r($retornoFat);
            echo '</pre>';
            exit(); // Encerra a execução do script
        }
    }
}
