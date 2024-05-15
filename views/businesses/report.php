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

$this->registerJsFile(Url::base() . '/js/exportPdf.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->title = Yii::t('app', 'Businesses Report');
$this->params['breadcrumbs'][] = $this->title;
$initialValueCity = null;
if ($searchModel->city) {
      $city =  City::find()->where('id = :city', [':city' => $searchModel->city])->one();
    $initialValueCity = $city->name . ' - ' . $city->state->abbreviation;
}
?>

<?php
    yii\bootstrap\Modal::begin([
        'id' =>'modal',
        'header' => '<h1>' . Yii::t('app','Consumables') . '</h1>',
    ]);

    yii\bootstrap\Modal::end();
?>

<a class="btn btn-secondary default" href="/businesses/report-export" data-role="export" id="exportButton">Exportar</a>

<div class="business-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
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
                'filter' => AjaxSelect2::widget([
                    'attribute' => 'city',
                    'model' => $searchModel,
                    'ajaxUrl' => Url::to(['api/cities/index']),
                    'ajaxData' => new JsExpression('function(params) { return { CitySearch: {name: params.term}, page: params.page}; }'),
                    'initValueText' => $initialValueCity,
                    'templateResult' => new JsExpression('function(city) { return city.name; }'),
                    'templateSelection' => new JsExpression('function (city) { return city.text || city.name; }'),
                ]),
                'value' => function($model) {
                    return $model->legalPerson->city->name . ' - ' . $model->legalPerson->city->state->abbreviation;
                }
            ],
            [
                'header' => Yii::t('app', 'Fees'),
                'class' => 'yii\grid\ActionColumn',
                    'buttons' => [
                        'informations' => function ($url, $model, $key) {
                        return Html::a(FA::icon('list'), ['businesses/consumables-info', 'id' => $model->id], ['data-modal' => 'popupModal'] );
                        },
                     ],
                'template' => '{informations}'
            ],
        ],
    ]); ?>

</div>

<?php $this->registerJs(
    "$(function() {
        $('[data-modal=\"popupModal\"]').click(function(e) {
            e.preventDefault();
            $('#modal').modal('show').find('.modal-body')
            .load($(this).attr('href'));
        });
    });"
);?>
