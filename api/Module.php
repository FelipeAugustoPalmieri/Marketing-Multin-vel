<?php

namespace app\api; // Define o namespace do módulo, que ajuda a organizar o código e evitar conflitos de nomes

use Yii; // Importa a classe Yii, que é a principal classe da estrutura Yii e oferece várias funcionalidades
use app\models\LoginForm; // Importa a classe LoginForm, que é usada para autenticação de usuários
use yii\web\ForbiddenHttpException; // Importa a classe ForbiddenHttpException para lançar exceções HTTP 403

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\api\controllers'; // Define o namespace padrão para os controladores deste módulo

    public function init()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON; // Define o formato de resposta como JSON
        parent::init(); // Chama o método init da classe pai

        // Verifica se os parâmetros 'username' e 'token' estão presentes na requisição
        if (isset(Yii::$app->request->queryParams['username']) && isset(Yii::$app->request->queryParams['token'])) {
            $model = new LoginForm(); // Cria uma nova instância do modelo LoginForm
            $model->username = Yii::$app->request->queryParams['username']; // Define o nome de usuário
            $modelUser = $model->getUser(); // Obtém o usuário correspondente ao nome de usuário

            // Verifica se o token é válido
            if (password_verify(Yii::$app->request->queryParams['username'] . "tbestsistema", Yii::$app->request->queryParams['token'])) {
                Yii::$app->user->login($modelUser, 0); // Faz o login do usuário
            } else {
                throw new ForbiddenHttpException('You must be authenticated to have access to this page.'); // Lança uma exceção se o token for inválido
            }
        }

        $acessaApi = true; // Variável para determinar se o acesso à API é permitido
        if (!Yii::$app->user->isGuest) { // Verifica se o usuário não é visitante (está logado)
            // Verifica se a origem da requisição contém 'asaas'
            $acessaApi = (strpos(\Yii::$app->request->getOrigin(), "asaas") === false ? false : true);
        }

        // Verifica se o usuário é visitante e se os parâmetros 'CitySearch' ou 'ConsumerSearch' não estão presentes na requisição
        if (Yii::$app->user->isGuest && !isset(Yii::$app->request->queryParams["CitySearch"]) && !isset(Yii::$app->request->queryParams["ConsumerSearch"]) && !$acessaApi) {
            throw new ForbiddenHttpException('You must be authenticated to have access to this page.'); // Lança uma exceção se as condições não forem atendidas
        }
    }
}
