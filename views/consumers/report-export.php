<?php
use app\models\LegalPerson;
use app\models\TransactionReport;
use app\helpers\TransactionReportHelper;
use yii\helpers\Html;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Consumers Extract');
$this->params['breadcrumbs'][] = $this->title;

?>
<?php if($xShowCabecalho){ ?>
    <h3 align="center"><?= Html::encode($this->title) ?></h3>
<?php } ?>
<div class="table-responsive">
    <?php foreach ($ordem as $key => $value) {  ?>
        <table class="table table-bordered">
            <thead>
                <?php $dataperiodo = $value['periodo']; ?>
                <?php if($xShowCabecalho){ ?>
                    <tr>
                        <th colspan="<?php echo $xListaVenda? 4: 3; ?>" align="center" ><h3><?= $value['titulo']; ?></h3></th>
                        <th colspan="2" align="center"><?php echo $dataperiodo; ?></th>
                    </tr>
                <?php } ?>
            </thead>
            <tbody>
                <?php $contador = 0; ?>
                <?php if($ordem[$key]['dados'] != null){ ?>
                    <?php foreach ($ordem[$key]['dados'] as $key => $value) { ?>
                        <?php if($xImpressDeposit && $key == 0 && $xShowCabecalho){ ?>
                            <tr><th colspan="<?php echo $xListaVenda? 6: 5; ?>" > <hr> </th></tr>
                        <?php } ?>
                        <tr>
                            <th colspan="3"><?= $value->identifier." - ".$value->legalPerson->name." - ".$value->legalPerson->person->cpf; ?></th>
                            <th colspan="<?php echo $xListaVenda? 3: 2; ?>" align="right"><?= $dataperiodo; ?></th>
                        </tr>
                        <tr>
                            <?php if ($xInfConta){ ?>
                                <td colspan="<?php echo $xListaVenda? 3: 2; ?>" style="text-align: center;">Banco</td>
                                <td style="text-align: center;">Agência</td>
                                <td width="20" style="text-align: center;">Conta</td>
                                <td width="20" style="text-align: center;">Op</td>
                            <?php } ?>
                        </tr>
                        <?php if ($xInfConta){ ?>
                            <tr>
                                <td colspan="<?php echo $xListaVenda? 3: 2; ?>" style="text-align: center;"><?= $value->bank_name." - ".$value->bank_number; ?></td>
                                <td style="text-align: center;"><?= $value->bank_agency; ?></td>
                                <td width="20" style="text-align: center;"><?= $value->bank_account; ?></td>
                                <td width="20" style="text-align: center;"><?= $value->operation; ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="<?php echo $xListaVenda? 2: 1; ?>" style="height:5px;" >Indicações</td>
                            <td style="height:5px;" >Pessoal</td>
                            <td style="height:5px;" >Rede</td>
                            <td style="height:5px;" colspan="2" style="font-size: 18px;text-align: right;">Total</td>
                        </tr>
                        <tr>
                            <td colspan="<?php echo $xListaVenda? 2: 1; ?>" width="20"><?= \Yii::$app->formatter->asCurrency(TransactionReport::getTotalActivationPeriodo($dataInicio, $dataFinal, $value->id)); ?></td>
                            <td width="20"><?= \Yii::$app->formatter->asCurrency(TransactionReport::getTotalSaleHimPeriodo($dataInicio, $dataFinal, $value->id)); ?></td>
                            <td width="20"><?= \Yii::$app->formatter->asCurrency(TransactionReport::getTotalSaleNetPeriodo($dataInicio, $dataFinal, $value->id)); ?></td>
                            <td colspan="2" width="20" style="font-size: 17px; text-align: right; font-weight: bold;"><?= \Yii::$app->formatter->asCurrency(TransactionReport::getTotalPeriodo($dataInicio, $dataFinal, $value->id)); ?></td>
                        </tr>
                        <?php if ($xListaVenda){ ?>
                            <tr>
                                <th colspan="6" style="text-align: center; font-size: 18px;">Extrato</th>
                            </tr>
                            <tr>
                                <td style="width: 100px;"><?= Yii::t('app', 'Created At'); ?></td>
                                <td style="width: 80px;"><?= Yii::t('app', 'Transaction'); ?></td>
                                <td><?= Yii::t('app', 'Origin'); ?></td>
                                <td><?= Yii::t('app', 'Description'); ?></td>
                                <td><?= Yii::t('app', 'Total'); ?></td>
                                <td><?= Yii::t('app', 'Profit'); ?></td>
                            </tr>
                            <?php foreach(TransactionReport::getTransactionReportExterno($value->id, $dataInicio, $dataFinal) as $key=>$value){ ?>
                                <tr>
                                    <td style="width: 100px;" ><?= \Yii::$app->formatter->asDatetime(new DateTime($value->created_at)); ?></td>
                                    <td style="width: 80px;"><?= TransactionReportHelper::getTransactionType($value); ?></td>
                                    <td><?= TransactionReportHelper::getTransactionOrigin($value); ?></td>
                                    <td><?= TransactionReportHelper::getTransactionDescription($value); ?></td>
                                    <td><?= \Yii::$app->formatter->asCurrency(TransactionReportHelper::getSaleValue($value)); ?></td>
                                    <td><?= \Yii::$app->formatter->asCurrency($value->profit); ?></td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                        <?php if($xImpressDeposit && $contador < 3){ ?>
                            <tr><th colspan="<?php echo $xListaVenda? 6: 5; ?>" > <hr> </th></tr>
                        <?php $contador++; }else{ echo "<tr><th></th></tr>"; $contador = 0;} ?>
                    <?php } ?>
                <?php }else{ ?>
                    <tr>
                        <td colspan="5" align="center" >Nenhum Registro Encontrado</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>
<!--<div class="sales-report">
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
    </table>
</div>-->
