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
use app\models\InvestimentoDetailReport;

$this->registerJsFile(Url::base() . '/js/exportPdf.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->title = Yii::t('app', 'Extract Porcentagem');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="configuration-index">
    <h1><?= Html::encode($this->title) ?></h1>
</div>
<div class="form card card-block">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => Url::to(['investimento/extract']),
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
            <div class="col-md-4 col-xs-4">
                <?= $form->field($model, 'inicio_periodo')->input('date', ['class' => 'form-control input-datepicker']) ?>
            </div>
            <div class="col-md-4 col-xs-4">
                <?= $form->field($model, 'fim_periodo')->input('date', ['class' => 'form-control input-datepicker']) ?>
            </div>

            <div class="col-md-3 col-xs-4" style="padding-top: 25px;">
                <?= Html::submitButton(Yii::t('app', 'Generate'), ['class' => 'btn btn-primary']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
</div>
<a class="btn btn-secondary button-red" href="extract-export" target="_blank" data-role="export" id="exportButton">Exportar</a>
<?= GridView::widget([
    'dataProvider' => $model->getTransactionReport(),
    'exportable' => false,
    'showFooter' => true,
    'pager' => ['class' => LinkPager::className()],
    'columns' => [
        [
            'format' => 'raw',
            'attribute' => 'investiment_at',
            'value' => function($model) {
                return \Yii::$app->formatter->asDatetime(new DateTime($model->investiment_at));
            }
        ],
        'invoice_code',
        [
            'format' => 'raw',
            'attribute'=>'consumer_id',
            'value'=>function($model){
                return $model->consumer->legalPerson->name;
            },
            'footer' => (
                '<span class="footer-report">Total Investimento: <span class="footer-report-value">' . \Yii::$app->formatter->asCurrency(InvestimentoDetailReport::getTotalInvestimento($model->inicio_periodo, $model->fim_periodo,$model->consumer_id)) .'</span></span>'
            )
        ],
        [
            'format' => 'raw',
            'attribute' => 'total',
            'value' => function($model){
                return \Yii::$app->formatter->asCurrency($model->total);
            },
            'footer' => (
                '<span class="footer-report">Total Juros: <span class="footer-report-value">' . \Yii::$app->formatter->asCurrency(InvestimentoDetailReport::getTotalJuros($model->inicio_periodo, $model->fim_periodo,$model->consumer_id)) .'</span></span>'
            )
        ],
        [
            'format' => 'raw',
            'attribute' => 'Saldo',
            'value' => function($model){
                return \Yii::$app->formatter->asCurrency(InvestimentoDetailReport::getSaldoLinha($model->id));
            },
            'footer' => (
                '<span class="footer-report">Total: <span class="footer-report-value">' . \Yii::$app->formatter->asCurrency(InvestimentoDetailReport::getTotal($model->inicio_periodo, $model->fim_periodo, $model->consumer_id)) .'</span></span>'
            )
        ]
    ],
]); ?>