<?php
namespace app\api\controllers;

use app\models\search\ConsumerSearch;
use Yii;
use yii\rest\ActiveController;

class ConsumersController extends ActiveController
{
    public $modelClass = 'app\models\Consumer';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = function ($action) {
            $searchModel = new ConsumerSearch;
            return $searchModel->search(Yii::$app->request->queryParams);
        };

        return [
            'index' => $actions['index'],
            'options' => $actions['options'],
        ];
    }
}
