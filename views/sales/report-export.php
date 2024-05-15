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
use app\models\SalesReport;

$this->title = Yii::t('app', 'Sales Report');
$this->params['breadcrumbs'][] = $this->title;

?>
<h1 align="center"><?= Html::encode($this->title) ?></h1>
<div>
    <?php if(isset($legalPerson)){ ?>
        <table style="width:100%; margin-bottom:10px;">
            <tr style="font-size: 18px;">
                <td style="width: 100px;">Empresa:</td>
                <td><span style="font-weight: bold;"><?= $legalPerson->getName(); ?></span></td>
            </tr>
            <tr>
                <td>CNPJ:</td>
                <td><span style="font-weight: bold;"><?= $legalPerson->getNationalIdentifier(); ?></span></td>
            </tr>
            <tr style="font-size: 18px;">
                <td>Telefone:</td>
                <td><span style="font-weight: bold;"><?= $legalPerson->getPhoneNumber(); ?></span></td> 
            </tr>
            <tr style="font-size: 18px;">
                <td>E-mail:</td>
                <td><span style="font-weight: bold;"><?= $legalPerson->email; ?></span></td> 
            </tr>
        </table>
    <?php } ?>
</div>
<div class="sales-report">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Total Vendas</th>
                <?php if (Yii::$app->user->can('admin') && isset($model->businessObject)) { ?>
                    <th>Convênio</th>
                <?php } ?>
                <th>Período</th>
                <th align="right">Valor Total</th>
                <th align="right">Valor Repasse Total</th>
            </tr>
        </thead>
        <tbody>
            <tr style="font-weight: bold;">
                <td><?= $dados['count']; ?> Vendas</td>
                <?php if (Yii::$app->user->can('admin') && isset($model->businessObject)) { ?>
                <td><span class="footer-report-value"><?= $model->businessObject->legalPerson->name; ?></span></td>
                <?php } ?>
                <td><span class="footer-report-value"><?= $dados['dataInicial'] . ' à ' . $dados['dataFinal']; ?></span></td>
                <td align="right"><span style="font-weight: bold;"><?= $dados['valorTotal']; ?></span></td>
                <td align="right"><span style="font-weight: bold;"><?= $dados['valorRemessa']; ?></span></td>
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
                'attribute' => 'consumer.identifier',
                'contentOptions' => ['style' => 'width:70px;'],
                'value' => function($model) {
                    return $model->consumer->identifier;
                }
            ],
            [
                'attribute' => 'sold_at',
                'contentOptions' => ['style' => 'width:200px;'],
                'value' => function($model) {
                    return \Yii::$app->formatter->asDatetime(new DateTime($model->sold_at), 'dd/MM/yyyy HH:mm:ss');
                },
            ],
            [
                'attribute' => 'invoice_code',
                'contentOptions' => ['style'=>'text-align:right']
            ],
            [
                'attribute' => 'total',
                'contentOptions' => ['style'=>'text-align:right'],
                'value' => function($model) {
                    return \Yii::$app->formatter->asCurrency($model->total);
                }
            ],
            [
                'attribute' => 'fees',
                'contentOptions' => ['style'=>'text-align:right'],
                'value' => function($model) {
                    return \Yii::$app->formatter->asCurrency($model->fees);
                }
            ],
        ],
    ]); ?>
</div>
