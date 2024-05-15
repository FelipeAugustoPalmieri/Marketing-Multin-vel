<?php
namespace app\api\controllers;

use app\models\search\CitySearch;
use Yii;
use yii\rest\ActiveController;

class CitiesController extends ActiveController
{
    public $modelClass = 'app\models\City';
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
            $searchModel = new CitySearch;
            return $searchModel->search(Yii::$app->request->queryParams);
        };

        return [
            'index' => $actions['index'],
            'options' => $actions['options'],
        ];
    }
}
