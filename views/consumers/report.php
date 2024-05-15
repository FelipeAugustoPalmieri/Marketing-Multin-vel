<?php

use yii\helpers\Html;
use app\widgets\GridView;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use app\helpers\TransactionReportHelper;
use app\widgets\LinkPager;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Consumer;
use yii\helpers\Url;
use app\models\TransactionReport;

$this->title = Yii::t('app', 'Points Report');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="form card card-block">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => Url::to(['consumers/report']),
    ]); ?>
        <div class="row">
            <div class="col-sm-12">
                <?php if (in_array('admin', array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)))) {
                    echo $form->field($model, 'consumer_id')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(
                        Consumer::find()->all(),
                        'id',
                        function($item) {
                            return $item->identifier . ' - ' . $item->legalPerson->name . ' - ' . $item->legalPerson->nationalIdentifier;
                        }
                    ),
                    'theme' => Select2::THEME_BOOTSTRAP,
                            'options' => ['placeholder' => 'Digite nome, CPF ou Código ...'],
                    'pluginOptions' => ['allowClear' => true],
                ]); } else {
                        $consumerUser = Consumer::find()->where(['id' => Yii::$app->user->identity->authenticable_id])->one();
                        echo $form->field($model, 'consumer_id')->dropDownList(
                            [$consumerUser->id => $consumerUser->legalPerson->name],
                            ['class' => 'form-control c-select']
                        );
                    } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-xs-6">
                <?= $form->field($model, 'period')->input('date', ['class' => 'form-control input-datepicker', 'type' => 'month']) ?>
            </div>

            <div class="col-md-3 col-xs-4" style="padding-top: 35px;">
                <?= Html::submitButton(Yii::t('app', 'Generate'), ['class' => 'btn btn-primary']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
</div>

<?= GridView::widget([
    'dataProvider' => $model->getTransactionReport(),
    'exportable' => true,
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
            'header' => Yii::t('app', 'Origin'),
            'value' => function($model) {
                return TransactionReportHelper::getTransactionOrigin($model);
            },
            'footer' => !isset($generateCsv) ?
                        ('<span class="footer-report">Total Indicação: <span class="footer-report-value">' . \Yii::$app->formatter->asCurrency(TransactionReport::getTotalActivation($model->period, $model->consumer_id)) .'</span></span>') :
                        ('Total Indicação: ' . \Yii::$app->formatter->asCurrency(TransactionReport::getTotalActivation($model->period, $model->consumer_id)))
        ],
        [
            'format' => 'raw',
            'header' => Yii::t('app', 'Description'),
            'value' => function($model) {
                return TransactionReportHelper::getTransactionDescription($model);
            },
            'footer' => !isset($generateCsv) ?
                        ('<span class="footer-report">Total Compra Própria: <span class="footer-report-value">' . \Yii::$app->formatter->asCurrency(TransactionReport::getTotalSaleHim($model->period, $model->consumer_id)) .'</span></span>') :
                        ('Total Compra Própria: ' . \Yii::$app->formatter->asCurrency(TransactionReport::getTotalSaleHim($model->period, $model->consumer_id)))
        ],
        [
            'format' => 'raw',
            'header' => Yii::t('app', 'Total'),
            'value' => function($model) {
                return \Yii::$app->formatter->asCurrency(TransactionReportHelper::getSaleValue($model));
            },
            'footer' => !isset($generateCsv) ?
                        ('<span class="footer-report">Total Compra Rede: <span class="footer-report-value">' . \Yii::$app->formatter->asCurrency(TransactionReport::getTotalSaleNet($model->period, $model->consumer_id)) .'</span></span>') :
                        ('Total Compra Rede: ' . \Yii::$app->formatter->asCurrency(TransactionReport::getTotalSaleNet($model->period, $model->consumer_id)))
        ],
        [
            'format' => 'raw',
            'attribute' => 'profit',
            'value' => function($model) {
                return \Yii::$app->formatter->asCurrency($model->profit);
            },
            'footer' => !isset($generateCsv) ?
                        ('<span class="footer-report">Total: <span class="footer-report-value">' . \Yii::$app->formatter->asCurrency(TransactionReport::getTotal($model->period, $model->consumer_id)) .'</span></span>') :
                        ('Total: ' . \Yii::$app->formatter->asCurrency(TransactionReport::getTotal($model->period, $model->consumer_id)))

        ],
    ],
]); ?>
