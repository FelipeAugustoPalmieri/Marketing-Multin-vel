<?php
use yii\helpers\Url;
use yii\helpers\Html;
use app\widgets\Alert;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('app', 'Plane pay');
$this->registerJsFile(Url::base() . '/js/gerar-contrato-investimento.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<div class="container">
    <h1 class="text-center"><?= Html::encode($this->title) ?></h1> 
    <div class="row">
        <div class="col-sm-12">
            <?= Alert::widget() ?>
        </div>
    </div>
    <div class="row featurette" style="margin-bottom: 30px;">
        <div class="col-md-12 text-center">
            <h2 class="featurette-heading"><span class="text-muted">Agora você faz parte de um grupo inteligente de consumidores.</span></h2>
            <h3>Abaixo você pode baixar o Contrato de Investimento ou Visualizar a fatura</h3>
            <div style="margin-top: 30px;">
                <button type="button" id="download" data-id="<?=$consumerId; ?>" class="btn btn-success btn-lg">Download Contrato</button>
                <button type="button" id="visualizarfatura" data-urlinvoice="<?=$invoiceUrl; ?>" class="btn btn-success btn-lg">Visualizar Fatura</button>
            </div>
        </div>
    </div>
</div>