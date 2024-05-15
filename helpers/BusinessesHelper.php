<?php
namespace app\helpers;

use Yii;
use app\models\Business;

class BusinessesHelper
{
    public static function getConsumables(Business $model)
    {
        $html = '';

        foreach($model->consumables as $consumable) {
            $html .= $consumable->description. ' - ' . Yii::$app->formatter->asPercent($consumable->shared_percentage / 100, 2) . '<br/>';
        }
        return $html;

    }
}