<?php

use yii\helpers\Html;
use app\widgets\GridView;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use app\widgets\LinkPager;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Consumer;
use app\models\Sale;
use app\widgets\AjaxSelect2;
use yii\helpers\Url;
use yii\web\JsExpression;
use rmrevin\yii\fontawesome\FA;

$this->registerJsFile(Url::base() . '/js/representativeReport.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/js/tree-modal.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->title = Yii::t('app', 'Points Report');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="form card card-block">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
    ]); ?>
        <div class="row">
            <div class="col-md-4 col-xs-6">
                <?= $form->field($modelNetReport, 'period')->input('date', ['class' => 'form-control input-datepicker', 'type' => 'month']) ?>
            </div>

            <div class="col-md-3 col-xs-4" style="padding-top: 35px;">
                <?= Html::submitButton(Yii::t('app', 'Generate'), ['class' => 'btn btn-primary', 'id' => 'bt-submit']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
</div>


<?php
    yii\bootstrap\Modal::begin(['id' =>'modal']);
    yii\bootstrap\Modal::end();
?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'exportable' => true,
    'showFooter' => true,
    'pager' => ['class' => LinkPager::className()],
    'columns' => [
            [
                'attribute' => 'identifier',
                'value' => function($model) {
                    return $model->identifier;
                }
            ],
            [
                'attribute' => 'name',
                'value' => function($model) {
                    return $model->legalPerson->getName();
                }
            ],
            [
                'header' => Yii::t('app', 'Points'),
                'value' => function($model) use ($modelNetReport) {
                    return $model->getMonthPoints($modelNetReport->month, $modelNetReport->year, $model->id);
                }
            ],
            [
                'header' => Yii::t('app', 'Missing points'),
                'value' => function($model) use ($modelNetReport)  {

                    if (!$model->plane) {
                        return null;
                    }

                    $monthPoints = $model->getMonthPoints($modelNetReport->month, $modelNetReport->year, $model->id);

                    if ($monthPoints >= $model->plane->goal_points){
                        return 'Meta Atingida';
                    }

                    return  $model->plane->goal_points - $monthPoints;
                }
            ],
            [
                'header' => Yii::t('app', 'Indications'),
                'value' => function($model) use ($modelNetReport) {
                    return $model->getMonthIndications($modelNetReport->month, $modelNetReport->year, $model->id);
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                    'buttons' => [
                        'informations' => function ($url, $model, $key) {
                        return Html::a(FA::icon('address-book-o'), ['consumers/informations', 'id' => $model->id], ['data-modal' => 'popupModal'] );
                        },
                     ],
                'template' => '{informations}'
            ],
    ],
  ]);
?>

<?php $this->registerJs(
    "$(function() {
        $('[data-modal=\"popupModal\"]').click(function(e) {
            e.preventDefault();
            $('#modal').modal('show').find('.modal-body')
            .load($(this).attr('href'));
        });
    });"
); ?>