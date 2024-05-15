<?php
use app\models\LegalPerson;
use app\widgets\LinkPager;
use yii\helpers\Html;
use app\widgets\GridView;
use rmrevin\yii\fontawesome\FA;
use app\widgets\AjaxSelect2;
use app\widgets\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;
use app\models\City;
use app\models\Consumable;
use app\helpers\BusinessesHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\BusinessSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Businesses Report');
$this->params['breadcrumbs'][] = $this->title;

?>
<h1 align="center"><?= Html::encode($this->title) ?></h1>
<div class="business-report">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'exportable' => false,
        'pager' => ['class' => LinkPager::className()],
        'columns' => [
            [
                'attribute' => 'name',
                'options' => ['width' => '20%'],
                'value' => function($model) {
                    return $model->getName();
                }
            ],
            [
                'attribute' => 'economic_activity',
                'header' => Yii::t('app', 'Economic Activity'),
                'value' => function($model) {
                    return $model->getEconomicActivity();
                }
            ],
            [
                'attribute' => 'cell_number',
                'options' => ['width' => '13%'],
                'value' => function($model) {
                    return $model->getPhoneNumber();
                }
            ],
             [
                'header' => Yii::t('app', 'Address'),
                'attribute' => 'address',
                'options' => ['width' => '20%'],
                'value' => function($model) {
                    return $model->legalPerson->address;
                }
            ],
            [
                'header' => Yii::t('app', 'City'),
                'value' => function($model) {
                    return $model->legalPerson->city->name . ' - ' . $model->legalPerson->city->state->abbreviation;
                }
            ],
            [
                'header' => Yii::t('app', 'Fees'),
                'format' => 'raw',
                'value' => function($model) {
                    return BusinessesHelper::getConsumables($model);
                },
            ],
        ],
    ]); ?>
</div>
