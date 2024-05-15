<?php
use app\widgets\Alert;
use app\widgets\AjaxSelect2;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;
use yii\bootstrap\ToggleButtonGroup;
use app\models\PlanoInvestimento;
use app\models\Consumer;

//$this->registerJsFile(Url::base() . '/js/jquery.maskMoney.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
//$this->registerJsFile(Url::base() . '/js/contrato-investimento.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
(\Yii::$app->devicedetect->isMobile() == false ? $isMobile = 0 : $isMobile = 1);
$this->title = Yii::t('app', 'Gerar Contrato');
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="business-form" class="">
   <fieldset class="data-separator">
        <legend><?= Yii::t('app', 'Gerar Contrato'); ?></legend>
        <div class="row">
            <div class="col-sm-12">
                <?= Alert::widget() ?>
            </div>
        </div>
        <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>
        <div class="row">         
            <div class="col-sm-12">
                <?php if (in_array('admin', array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)))) {
                    echo $form->field($investimento, 'consumer_id')->widget(Select2::classname(), [
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
                        echo $form->field($investimento, 'consumer_id')->dropDownList(
                            [$consumerUser->id => $consumerUser->legalPerson->name],
                            ['class' => 'form-control c-select']
                        );
                    } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <?= $form->field($investimento, 'dia_vencimento')->textInput(['maxlength' => 2]) ?>
            </div>
            <div class="col-sm-4">
                <?= $form->field($investimento, 'prazo')->dropDownList($listParcelas, ['class' => 'form-control c-select']); ?>
            </div>
            <div class="col-sm-4">
                <?= $form->field($investimento, 'valor')->dropDownList($valores, ['class' => 'form-control c-select']); ?>
            </div>
        </div>
        <?php if (in_array('admin', array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)))) { ?>
            <div class="row">
                <div class="col-sm-4">
                    <?php 
                        if($isMobile){
                            echo $form->field($investimentoForm, 'data_contrato')->textInput(['type' => 'date']);
                        }else{
                            echo $form->field($investimentoForm, 'data_contrato')->textInput()->widget(MaskedInput::className(), ['mask' => '99/99/9999']);
                        }
                    ?>
                </div>
                <div class="col-sm-4">
                    <?php if(!$isMobile)
                        echo $form->field($investimentoForm, 'valor_contrato')->textInput(['maxlength' => 255, 'data-prefix' => 'R$ ', 'data-thousands' => '', 'data-decimal' => ',']);
                    else {
                        echo $form->field($investimentoForm, 'valor_contrato')->textInput(['maxlength' => 255, 'placeholder' => 'R$ 0,00', 'type' => 'number']);
                    } ?>
                </div>
            </div>
        <?php } ?>
        <div class="row">
            <div class="col-sm-2" style="padding-top: 23px;">
                <?= Html::submitButton(Yii::t('app', 'Gerar Contrato'), ['class' => 'btn btn-success', 'id' => 'btnGerarContrato']); ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </fieldset>

</div>

