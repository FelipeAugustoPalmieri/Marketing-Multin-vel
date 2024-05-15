<?php

use app\widgets\Alert;
use app\models\PhysicalPerson;
use app\models\Occupation;
use app\widgets\AjaxSelect2;
use app\widgets\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\MaskedInput;
use yii\helpers\BaseHtml;
use yii\helpers\Json;

$this->title = Yii::t('app', 'Create Consumer');
$this->registerJsFile(Url::base() . '/js/cadastro-fora.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/js/consumers.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
(\Yii::$app->devicedetect->isMobile() == false ? $isMobile = 0 : $isMobile = 1);
?>
<div class="container">
    <h1><?= Html::encode($this->title) ?></h1> 

    <?php $form = ActiveForm::begin([]); ?>

    <fieldset class="data-separator">
        <legend><?= Yii::t('app', 'General'); ?></legend>
        <div class="row">
            <div class="col-sm-12">
                <?= Alert::widget() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <?php
                    echo $form->field($physicalPerson, 'cpf')->textInput(['maxlength' => 14])->widget(MaskedInput::className(), ['mask' => '999.999.999-99']);
                ?>
            </div>
            <div class="col-sm-8">
                <?= $form->field($physicalPerson, 'name')->textInput(['maxlength' => 255]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4">
                <?php 
                    echo $form->field($physicalPerson, 'born_on')->textInput()->widget(MaskedInput::className(), ['mask' => '99/99/9999']);
                ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($physicalPerson, 'rg')->textInput(['maxlength' => 20])->widget(MaskedInput::className(), ['mask' => '99.999.999-9']) ?>
            </div>

            <div class="col-sm-3">
                <?= $form->field($physicalPerson, 'issuing_body')->textInput(['maxlength' => 255]) ?>
            </div>

            <div class="col-sm-3">
                <?php
                    echo $form->field($physicalPerson, 'pis')->textInput(['maxlength' => 14])->widget(MaskedInput::className(), ['mask' => '999.99999.99-9']);
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4">
                <?= $form->field($physicalPerson, 'nationality')->textInput(['maxlength' => 255]) ?>
            </div>

            <div class="col-sm-4">
                <?= $form->field($physicalPerson, 'occupation_id')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(
                        Occupation::find()->all(),
                        'id',
                        function($item) {
                            return $item->name;
                        }
                    ),
                ]) ?>
            </div>

            <div class="col-sm-4">
                <?= $form->field($physicalPerson, 'marital_status')->dropDownList(
                    ['' => ''] + array_combine(PhysicalPerson::getMaritalStatusList(), PhysicalPerson::getMaritalStatusList()),
                    ['class' => 'form-control c-select']
                ) ?>
            </div>
        </div>

    </fieldset>

    <fieldset class="data-separator">
        <legend><?= Yii::t('app', 'Address'); ?></legend>

        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($legalPerson, 'email')->textInput(['maxlength' => 255, 'type' => 'email']) ?>
            </div>

            <div class="col-sm-6">
                <?= $form->field($legalPerson, 'website')->textInput(['maxlength' => 255, 'type' => 'url']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-3">
                <?= $form->field($legalPerson, 'zip_code')->textInput(['maxlength' => 9])->widget(MaskedInput::className(), ['mask' => '99999-999']) ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($legalPerson, 'address')->textInput(['maxlength' => 255]) ?>
            </div>

            <div class="col-sm-3">
                <?= $form->field($legalPerson, 'address_complement')->textInput(['maxlength' => 255]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($legalPerson, 'district')->textInput(['maxlength' => 255]) ?>
            </div>

            <div class="col-sm-6">
                <?php
                $initialValueText = null;
                if ($legalPerson->city_id > 0) {
                    $initialValueText = $legalPerson->city->name . ' - ' . $legalPerson->city->state->abbreviation;
                }
                echo $form->field($legalPerson, 'city_id')->widget(AjaxSelect2::classname(), [
                    'ajaxUrl' => Url::to(['api/cities/index']),
                    'ajaxData' => new JsExpression('function(params) { return { CitySearch: {name: params.term}, page: params.page}; }'),
                    'initValueText' => $initialValueText,
                    'templateResult' => new JsExpression('function(city) { return city.name; }'),
                    'templateSelection' => new JsExpression('function (city) { return city.text || city.name; }'),
                ]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <?= $form->field($legalPerson, 'cell_number')->textInput(['maxlength' => 255])->widget(MaskedInput::className(), ['mask' => ['(99) 9999-9999', '(99) 99999-9999']]) ?>
            </div>
            <div class="col-sm-4">
                <?= $form->field($legalPerson, 'home_phone')->textInput(['maxlength' => 255])->widget(MaskedInput::className(), ['mask' => ['(99) 9999-9999', '(99) 99999-9999']]) ?>
            </div>
            <div class="col-sm-4">
                <?= $form->field($legalPerson, 'comercial_phone')->textInput(['maxlength' => 255])->widget(MaskedInput::className(), ['mask' => ['(99) 9999-9999', '(99) 99999-9999']]) ?>
            </div>
        </div>
    </fieldset>
    <fieldset class="data-separator">
        <legend><?= Yii::t('app', 'Business Information'); ?></legend>

        <div class="row">
            <div class="col-sm-4">
                <?= $form->field($consumer, 'bank_name')->textInput(['maxlength' => 255]) ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($consumer, 'bank_number')->textInput(['maxlength' => 255]) ?>
            </div>

            <div class="col-sm-2">
                <?= $form->field($consumer, 'bank_agency')->textInput(['maxlength' => 255]) ?>
            </div>

            <div class="col-sm-2">
                <?= $form->field($consumer, 'operation')->textInput(['maxlength' => 255]) ?>
            </div>

            <div class="col-sm-2">
                <?= $form->field($consumer, 'bank_account')->textInput(['maxlength' => 255]) ?>
            </div>
        </div>
    </fieldset>

    <fieldset id="partner-data-block" class="hidden-sm-up data-separator">
        <legend><?= Yii::t('app', 'Partner'); ?></legend>
        <div class="row">
            <div class="col-sm-8">
                <?= $form->field($physicalPerson, 'partner_name')->textInput(['maxlength' => 255]) ?>
            </div>

            <div class="col-sm-4">
                <?php 
                    echo $form->field($physicalPerson, 'partner_born_on')->textInput()->widget(MaskedInput::className(), ['mask' => '99/99/9999']);
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <?= $form->field($physicalPerson, 'partner_phone_number')->textInput(['maxlength' => 255])->widget(MaskedInput::className(), ['mask' => ['(99) 9999-9999', '(99) 99999-9999']]) ?>
            </div>

            <div class="col-sm-3">
                <?= $form->field($physicalPerson, 'partner_cpf')->textInput(['maxlength' => 14])->widget(MaskedInput::className(), ['mask' => '999.999.999-99']) ?>
            </div>

            <div class="col-sm-3">
                <?= $form->field($physicalPerson, 'partner_rg')->textInput(['maxlength' => 255]) ?>
            </div>

            <div class="col-sm-3">
                <?= $form->field($physicalPerson, 'partner_issuing_body')->textInput(['maxlength' => 255]) ?>
            </div>
        </div>
    </fieldset>
    <hr />
    <?= BaseHtml::checkbox('aceite_termo', false, ['label' => Yii::t('app', 'Terms', ['url' => Url::to(['termos'], ['target'=>'_blank'])])]); ?>
    <div class="form-group">
        <div class="text-sm-right">
            <?= Html::submitButton($consumer->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $consumer->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => 'btnCadastrar', 'disabled' => ($consumer->isNewRecord) ? 'disabled' : false]) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script>
var isNewRecord = '<?php $physicalPerson->isNewRecord ? '1' : '0'; ?>';
window.PARTNERED_MARITAL_STATUS = <?= Json::encode(PhysicalPerson::getMarriedMaritalStatusList()) ?>;
var textoSelecioneUm = '<?= Yii::t('app', 'Select option'); ?>';
</script>