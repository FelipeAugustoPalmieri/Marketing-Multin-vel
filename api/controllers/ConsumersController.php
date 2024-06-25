<?php
namespace app\api\controllers; // Define o namespace do controlador, que ajuda a organizar o código e evitar conflitos de nomes

use app\models\search\ConsumerSearch; // Importa a classe ConsumerSearch que provavelmente contém a lógica de busca para consumidores
use Yii; // Importa a classe Yii, que é a principal classe da estrutura Yii e oferece várias funcionalidades
use yii\rest\ActiveController; // Importa a classe ActiveController que fornece uma implementação base para controladores RESTful

class ConsumersController extends ActiveController
{
    // Define a classe do modelo que será usada por este controlador
    public $modelClass = 'app\models\Consumer';

    // Configura o serializador que formata a resposta da API
    public $serializer = [
        'class' => 'yii\rest\Serializer', // Define a classe do serializador
        'collectionEnvelope' => 'items', // Envolve coleções de modelos em uma chave chamada 'items'
    ];

    /**
     * @inheritdoc
     */
    public function actions()
    {
        // Obtém as ações padrão do ActiveController
        $actions = parent::actions();

        // Modifica a ação 'index' para usar um DataProvider personalizado
        $actions['index']['prepareDataProvider'] = function ($action) {
            // Cria uma instância do modelo de busca ConsumerSearch
            $searchModel = new ConsumerSearch;
            // Executa a busca com os parâmetros da query string e retorna o DataProvider resultante
            return $searchModel->search(Yii::$app->request->queryParams);
        };

        // Retorna as ações que este controlador deve suportar
        return [
            'index' => $actions['index'], // Ação para listar consumidores
            'options' => $actions['options'], // Ação para responder a requisições HTTP OPTIONS
        ];
    }
}
