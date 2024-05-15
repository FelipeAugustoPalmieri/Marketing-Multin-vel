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

$this->title = Yii::t('app', 'Extract Porcentagem');
$this->params['breadcrumbs'][] = $this->title;
?>
<h1 align="center" ><?= Html::encode($this->title) ?></h1>
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Total Investimento</th>
            <th>Período</th>
            <th align="right">Valor Total Investido</th>
            <th align="right">Valor Total Juros</th>
            <th align="right">Valor Total</th>
        </tr>
    </thead>
    <tbody>
        <tr style="font-weight: bold;">
            <td><?= $numerototal; ?> Investimento</td>
            <td><span class="footer-report-value"><?= $dataInicial . ' à ' . $dataFinal; ?></span></td>
            <td align="right"><span style="font-weight: bold;"><?= $totalinvestimento; ?></span></td>
            <td align="right"><span style="font-weight: bold;"><?= $totaljuros; ?></span></td>
            <td align="right"><span style="font-weight: bold;"><?= $total; ?></span></td>
        </tr>
    </tbody>
</table>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'exportable' => false,
    'showFooter' => false,
    'layout' => '{items}',
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
            }
        ],
        [
            'format' => 'raw',
            'attribute' => 'total',
            'value' => function($model){
                return \Yii::$app->formatter->asCurrency($model->total);
            }
        ],
        [
            'format' => 'raw',
            'attribute' => 'Saldo',
            'value' => function($model){
                return \Yii::$app->formatter->asCurrency(InvestimentoDetailReport::getSaldoLinha($model->id));
            },
        ]
    ],
]); ?>