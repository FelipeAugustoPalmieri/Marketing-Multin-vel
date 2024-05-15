<?php 
use app\widgets\LinkPager;
use app\widgets\GridView;
use app\models\RepresentativeComission;
use app\helpers\TransactionReportHelper;

$this->title = Yii::t('app', 'Comission');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php if(isset($legalPerson)){ ?>
    <table style="width:100%; margin-bottom:10px;">
        <tr style="font-size: 18px;">
            <td style="width: 100px;">Representante: </td>
            <td style="width: 280px;"><span style="font-weight: bold;"><?= $legalPerson->getName(); ?></span></td>
            <?php if(isset($consumer)){ ?>
                <td style="text-align: right;">Banco: </td>
                <td style="text-align: right;"><?= $consumer->bank_name; ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td style="width: 100px;">CNPJ:</td>
            <td style="width: 280px;"><span style="font-weight: bold;"><?= $legalPerson->getNationalIdentifier(); ?></span></td>
            <?php if(isset($consumer)){ ?>
                <td style="text-align: right;">Ag / Conta: </td>
                <td style="text-align: right;"><?= $consumer->bank_agency." / ".$consumer->bank_account; ?></td>
            <?php } ?>
        </tr>
        <tr style="font-size: 18px;">
            <td style="width: 100px;">Telefone:</td>
            <td style="width: 280px;"><span style="font-weight: bold;"><?= $legalPerson->getPhoneNumber(); ?></span></td> 
            <?php if(isset($consumer)){ ?>
                <td style="text-align: right;">Operação: </td>
                <td style="text-align: right;"><?= $consumer->operation; ?></td>
            <?php } ?>
        </tr>
        <tr style="font-size: 18px;">
            <td style="width: 100px;">E-mail:</td>
            <td style="width: 280px;"><span style="font-weight: bold;"><?= $legalPerson->email; ?></span></td> 
            <td style="text-align: right;">Total Comissão: </td>
            <td style="text-align: right;"><span style="font-weight: bold;"><?= Yii::$app->formatter->asCurrency(RepresentativeComission::getTotalComission($model->period, $model->consumer_representative_id)); ?></span></td>
        </tr>
    </table>
<?php } ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'exportable' => false,
    'showFooter' => false,
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
            }
        ],
        [
            'format' => 'raw',
            'header' => Yii::t('app', 'Comission'),
            'value' => function($model) {
                return \Yii::$app->formatter->asCurrency($model->profit);
            },
            'footer' => '<span class="footer-report">Total Comissão: <span class="footer-report-value">' . 
            \Yii::$app->formatter->asCurrency(RepresentativeComission::getTotalComission($model->period, $model->consumer_representative_id)) .'</span></span>'

        ],
    ],
]); ?>