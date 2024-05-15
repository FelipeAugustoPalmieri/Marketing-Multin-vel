<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\LinkPager;
use app\widgets\GridView;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Business;
use app\models\legalPerson;
use app\models\SalesReport;

$this->registerJsFile(Url::base() . '/js/salesReport.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/js/exportPdf.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->title = Yii::t('app', 'Sales Report');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="form card card-block">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => Url::to(['sales/report']),
    ]); ?>

        <div class="row">
            <div class="col-md-8 col-xs-4">
                <?= $form->field($model, 'convenio')->widget(Select2::classname(), [
                    'data' => !Yii::$app->user->can('admin') ?
                        ArrayHelper::map(
                            Business::find()->where("id = $model->convenio")->all(),
                            'id',
                            function($item) {
                                return $item->legalPerson->name . ' - ' . $item->legalPerson->nationalIdentifier;
                            }
                        ) :
                        ArrayHelper::map(
                            Business::find()->where("is_disabled = false")->all(),
                            'id',
                            function($item) {
                                return $item->legalPerson->name . ' - ' . $item->legalPerson->nationalIdentifier;
                            }
                        ),
                    'theme' => Select2::THEME_BOOTSTRAP,
                    'readonly' => !Yii::$app->user->can('admin') ? true : false,
                    'options' => ['placeholder' => 'Digite nome ou CPF/CNPJ ...'],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-xs-4">
                <?= $form->field($model, 'inicio_periodo')->input('date', ['class' => 'form-control input-datepicker']) ?>
            </div>

            <div class="col-md-3 col-xs-4">
                <?= $form->field($model, 'fim_periodo')->input('date', ['class' => 'form-control input-datepicker']) ?>
            </div>

            <div class="col-md-3 col-xs-4" style="padding-top: 35px;">
                <?= Html::submitButton(Yii::t('app', 'Generate'), ['class' => 'btn btn-primary']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
</div>

<a class="btn btn-secondary button-red" href="/sales/report-export" target="_blank" data-role="export" id="exportButton">Exportar</a>

<?= GridView::widget([
    'dataProvider' => $model->getSalesReport(),
    'exportable' => false,
    'showFooter' => true,
    'pager' => ['class' => LinkPager::className()],
    'columns' => [
        [
            'format' => 'raw',
            'attribute' => 'consumer.identifier',
            'value' => function($model) {
                return $model->consumer->identifier;
            }
        ],
        [
            'format' => 'raw',
            'attribute' => 'sold_at',
            'value' => function($model) {
                return Yii::$app->user->can('admin') ? '<a href="#" class="alterdata" data-type="text" data-pk="'.$model->id.'" data-url="'.Url::to(['sales/update-data']).'" data-title="'.Yii::t('app','Alter Date').'">'.\Yii::$app->formatter->asDatetime(new DateTime($model->sold_at), 'dd/MM/yyyy HH:mm:ss').'</a>' : Yii::$app->formatter->asDatetime(new DateTime($model->sold_at), 'dd/MM/yyyy HH:mm:ss');
            },
            'footer' => (Yii::$app->user->can('admin') && isset($model->businessObject) ?
                        (
                            !isset($generateCsv) ?
                            ('<span class="footer-report">Convênio: <span class="footer-report-value">' . $model->businessObject->legalPerson->name . '</span></span>') :
                            ('Convênio: ' . $model->businessObject->legalPerson->name)
                        ) :
                        ''
            )
        ],
        [
            'format' => 'raw',
            'attribute' => 'invoice_code',
            'footer' => (Yii::$app->user->can('admin') && isset($model->inicio_periodo) ?
                        (
                          !isset($generateCsv) ?
                          ('<span class="footer-report">Período: <span class="footer-report-value">' . \Yii::$app->formatter->asDate($model->inicio_periodo,'dd/MM/yyyy') . ' a ' . \Yii::$app->formatter->asDate($model->fim_periodo,'dd/MM/yyyy') . '</span></span>') :
                          ('Período: ' . \Yii::$app->formatter->asDate($model->inicio_periodo,'dd/MM/yyyy') . ' a ' . \Yii::$app->formatter->asDate($model->fim_periodo,'dd/MM/yyyy'))
                        ) :
                        ''
            )
        ],
        [
            'format' => 'raw',
            'attribute' => 'total',
            'value' => function($model) {
                return Yii::$app->user->can('admin') ? '<a href="#" class="altertotal" data-type="text" data-pk="'.$model->id.'" data-url="'.Url::to(['sales/update-total']).'" data-title="'.Yii::t('app','Alter Total').'">'.\Yii::$app->formatter->asCurrency($model->total).'</a>' : Yii::$app->formatter->asCurrency($model->total);
            },
            'footer' => (!isset($generateCsv) ?
                        ('<span class="footer-report">Total: <span class="footer-report-value">' . \Yii::$app->formatter->asCurrency(SalesReport::getTotal($model->inicio_periodo, $model->fim_periodo, $model->convenio)) . '</span></span>') :
                        'Total: ' . \Yii::$app->formatter->asCurrency(SalesReport::getTotal($model->inicio_periodo, $model->fim_periodo, $model->convenio))
                        )
        ],
        [
            'format' => 'raw',
            'attribute' => 'fees',
            'contentOptions' => ['class' => 'repasse'],
            'value' => function($model) {
                return \Yii::$app->formatter->asCurrency($model->fees + $model->fees_adm);
            },
            'footer' => (!isset($generateCsv) ?
                        ('<span class="footer-report">Total: <span class="footer-report-value">' . \Yii::$app->formatter->asCurrency(SalesReport::getTotalFees($model->inicio_periodo, $model->fim_periodo, $model->convenio)) . '</span></span>') :
                        'Total: ' . \Yii::$app->formatter->asCurrency(SalesReport::getTotalFees($model->inicio_periodo, $model->fim_periodo, $model->convenio))
                        )

        ],
    ],
]); ?>
