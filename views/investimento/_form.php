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

$this->registerJsFile(Url::base() . '/js/jquery.maskMoney.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/js/porcentagem.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
(\Yii::$app->devicedetect->isMobile() == false ? $isMobile = 0 : $isMobile = 1);
?>


<div id="business-form" class="">
   <fieldset class="data-separator">
      <legend><?= Yii::t('app', 'Create Porcentagem'); ?></legend>
      <div class="row">
            <div class="col-sm-12">
               <?= Alert::widget() ?>
            </div>
      </div>
      <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>
         <div class="row">
            <div class="col-sm-4">
               <?= $form->field($model, 'data_referencia')->textInput(['type' => 'date']) ?>
            </div>
            <div class="col-sm-2">
               <?php if(!$isMobile)
                  echo $form->field($model, 'porcentagem')->textInput(['maxlength' => 100, 'data-prefix' => '', 'data-thousands' => '', 'data-decimal' => ',']);
               else {
                  echo $form->field($model, 'porcentagem')->textInput(['maxlength' => 100, 'placeholder' => '0,00', 'type' => 'number']);
               }?>
            </div>
            <div class="col-sm-4">
               <?= $form->field($planoinvestimento, 'id')->dropDownList(ArrayHelper::map(PlanoInvestimento::find()->all(), 'id', 'nome'),['id' => 'consumer-plane_investiment_id','class' => 'form-control c-select', 'prompt' => Yii::t('app', 'Select Plan Investiment'),  'data-target' => '#id-button'])->label(Yii::t('app', 'Select Plan')); ?>
            </div>
            <div class="row">
               <div class="col-sm-2 text-right" style="padding-top: 23px;">
                  <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => 'btnCadastrar']) ?>
               </div>
            </div>
         </div>
      <?php ActiveForm::end(); ?>
   </fieldset>
</div>

<script type="text/javascript">
    var isMobile = <?= $isMobile ?>;
</script>