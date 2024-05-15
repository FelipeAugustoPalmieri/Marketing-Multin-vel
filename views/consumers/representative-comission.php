<?php

use yii\helpers\Html;
use app\widgets\GridView;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use app\widgets\LinkPager;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Consumer;
use app\models\TransactionReport;
use app\models\SalesReport;
use app\models\RepresentativeComission;
use app\helpers\TransactionReportHelper;
use app\widgets\AjaxSelect2;
use yii\helpers\Url;
use yii\web\JsExpression;
use rmrevin\yii\fontawesome\FA;


$this->registerJsFile(Url::base() . '/js/exportPdf.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->title = Yii::t('app', 'Comission');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="form card card-block">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => Url::to(['consumers/representative-comission']),
    ]); ?>
        <div class="row">
            <div class="col-sm-6">
                <?php if (in_array('admin', array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)))) {
                    echo $form->field($modelRepresentativeComission, 'consumer_representative_id')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(
                        Consumer::find()->businessRepresentatives()->affiliationPaid()->all(),
                        'id',
                        function($item) {
                            return $item->identifier . ' - ' . $item->legalPerson->name . ' - ' . $item->legalPerson->nationalIdentifier;
                        }
                    ),
                    'theme' => Select2::THEME_BOOTSTRAP,
                    'options' => ['placeholder' => 'Digite nome, CPF ou Código ...'],
                    'pluginOptions' => ['allowClear' => true],
                ]); } else {
                        $consumerUser = Consumer::find()->where(['id' => Yii::$app->user->identity->authenticable_id])->businessRepresentatives()->affiliationPaid()->one();
                        echo $form->field($modelRepresentativeComission, 'consumer_representative_id')->dropDownList(
                            [$consumerUser->id => $consumerUser->legalPerson->name],
                            ['class' => 'form-control c-select']
                        );
                    } ?>
            </div>
             <div class="col-md-4 col-xs-6">
                <?= $form->field($modelRepresentativeComission, 'period')->input('date', ['class' => 'form-control input-datepicker', 'type' => 'month']) ?>
            </div>

            <div class="col-md-2 col-xs-4" style="padding-top: 35px;">
                <?= Html::submitButton(Yii::t('app', 'Generate'), ['class' => 'btn btn-primary', 'id' => 'bt-submit']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
</div>

<a class="btn btn-secondary button-red" href="/consumers/representative-comission-export" data-url="representative-comission-export" target="_blank" data-role="export" id="exportButton">Exportar</a>

<?= GridView::widget([
    'dataProvider' => $modelRepresentativeComission->getTransactionReport(),
    'exportable' => false,
    'showFooter' => true,
    'pager' => ['class' => LinkPager::className()],
    'columns' => [
        [
            'format' => 'raw',
            'attribute' => 'created_at',
            'value' => function($model) {
                return \Yii::$app->formatter->asDatetime(new DateTime($model->created_at));
            }
        ],
        [
            'format' => 'raw',
            'header' => Yii::t('app', 'Transaction'),
            'value' => function($model) {
                return TransactionReportHelper::getTransactionType($model);
            },
        ],
        [
            'format' => 'raw',
            'header' => Yii::t('app', 'Description'),
            'value' => function($model) {
                return TransactionReportHelper::getTransactionDescription($model);
            },
        ],
        [
            'format' => 'raw',
            'header' => Yii::t('app', 'Fees'),
            'value' => function($model) {
                if($model->object_type == 'Sale'){
                    return \Yii::$app->formatter->asCurrency($model->object->fees);
                } else {
                    return Yii::t('app', 'Does not have');
                }
            },
            /*'footer' => !isset($generateCsv) ?
                        ('<span class="footer-report">Total Comissão: <span class="footer-report-value">' . \Yii::$app->formatter->asCurrency(SalesReport::getTotalFees($modelRepresentativeComission->period, $modelRepresentativeComission->consumer_representative_id)) .'</span></span>') :
                        ('Total: ' . \Yii::$app->formatter->asCurrency(SalesReport::getTotalFees($modelRepresentativeComission->period, $modelRepresentativeComission->consumer_representative_id)))*/


        ],
        [
            'format' => 'raw',
            'header' => Yii::t('app', 'Comission'),
            'value' => function($model) {
                return \Yii::$app->formatter->asCurrency($model->profit);
            },
            'footer' => !isset($generateCsv) ?
                        ('<span class="footer-report">Total Comissão: <span class="footer-report-value">' . \Yii::$app->formatter->asCurrency(RepresentativeComission::getTotalComission($modelRepresentativeComission->period, $modelRepresentativeComission->consumer_representative_id)) .'</span></span>') :
                        ('Total: ' . \Yii::$app->formatter->asCurrency(RepresentativeComission::getTotalComission($modelRepresentativeComission->period, $modelRepresentativeComission->consumer_representative_id)))

        ],
    ],
]); ?>