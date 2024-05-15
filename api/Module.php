<?php

namespace app\api;

use Yii;
use app\models\LoginForm;
use yii\web\ForbiddenHttpException;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\api\controllers';

    public function init()
    {
    	\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        parent::init();

        if(isset(Yii::$app->request->queryParams['username']) && isset(Yii::$app->request->queryParams['token'])){
        	$model = new LoginForm();
        	$model->username = Yii::$app->request->queryParams['username'];
        	$modelUser = $model->getUser();

            if(password_verify(Yii::$app->request->queryParams['username']."tbestsistema",Yii::$app->request->queryParams['token'])){
                Yii::$app->user->login($modelUser, 0);
            }else{
                throw new ForbiddenHttpException('You must be authenticated to have access to this page.');
            }
        	
        }
        $acessaApi = true;
        if(!Yii::$app->user->isGuest){
            $acessaApi = (strpos(\Yii::$app->request->getOrigin(), "asaas") === false ? false : true);
        }

        if (Yii::$app->user->isGuest && !isset(Yii::$app->request->queryParams["CitySearch"]) && !isset(Yii::$app->request->queryParams["ConsumerSearch"]) && !$acessaApi) {
            throw new ForbiddenHttpException('You must be authenticated to have access to this page.');
        }
    }
}
