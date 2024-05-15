<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\widgets\GridView;
use dosamigos\datepicker\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Consumer;

$this->title = Yii::t('app', 'extract consumers');
$this->registerJsFile(Url::base() . '/js/exportPdf.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/js/jquery.maskMoney.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/js/extract.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="form card card-block">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => Url::to(['consumers/extract']),
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
            <div class="col-md-3 col-xs-4">
                <?= $form->field($model, 'inicio_periodo')->input('date', ['class' => 'form-control input-datepicker']) ?>
            </div>
            <div class="col-md-3 col-xs-4">
                <?= $form->field($model, 'fim_periodo')->input('date', ['class' => 'form-control input-datepicker']) ?>
            </div>
            <div class="col-md-3 col-xs-4">
                <?= $form->field($model, 'minimovalor')->textInput(['maxlength' => 255, 'data-prefix' => 'R$ ', 'data-thousands' => '', 'data-decimal' => ',']); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xs-6">
                <?= $form->field($model, 'xInfContaTotal')->checkbox(['label' => 'Mostrar Informação Conta Bancaria'])->label(false); ?>
                <?= $form->field($model, 'xListaVendas')->checkbox(['label' => 'Mostrar Lista De Vendas'])->label(false); ?>
                <?= $form->field($model, 'xInfConsumidor')->checkbox(['label' => 'Mostrar Informações Consumidor'])->label(false); ?>
            </div>
            <div class="col-md-6 col-xs-6">
                <?= $form->field($model, 'xOrderPlanos')->checkbox(['label' => 'Ordenar por plano de adesão'])->label(false); ?>
                <?= $form->field($model, 'xImpressDeposit')->checkbox(['label' => 'Mostrar Linha Entre Consumidores'])->label(false); ?>
                <?= $form->field($model, 'xShowCabecalho')->checkbox(['label' => 'Mostrar o Cabeçalho'])->label(false); ?>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
    <a class="btn btn-secondary button-red" href="report-export" target="_blank" data-role="export" id="exportButton">Gerar Extrato</a>
</div>
