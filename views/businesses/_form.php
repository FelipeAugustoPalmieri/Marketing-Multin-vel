<?php

use app\models\City;
use app\models\Consumer;
use app\models\LegalPerson;
use app\widgets\AjaxSelect2;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;
use yii\bootstrap\ToggleButtonGroup;

/* @var $this yii\web\View */
/* @var $business app\models\Business */
/* @var $legalPerson app\models\LegalPerson */
/* @var $physicalPerson app\models\PhysicalPerson */
/* @var $juridicalPerson app\models\JuridicalPerson */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile(Url::base() . '/js/businesses.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<div class="business-form">
    <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>

    <?php if ($business->isNewRecord || Yii::$app->user->can('admin')) { ?>
    <div id="legalperson-person_class">
        <div class="btn-group" data-toggle="buttons">
            <?php foreach(LegalPerson::getTypes() as $key=>$value){ ?>
                <?php if($legalPerson->person_class == $key){ ?>
                    <label class="btn btn-primary active focus">
                        <input type="radio" name="LegalPerson[person_class]" value="<?= $key; ?>" checked autocomplete="off"> <?= $value; ?>
                    </label>
                <?php } else{ ?>
                    <label class="btn btn-primary">
                        <input type="radio" name="LegalPerson[person_class]" value="<?= $key; ?>" autocomplete="off"> <?= $value; ?>
                    </label>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
    <?php }else{ ?>
        <?= $form->field($legalPerson, 'person_class')->hiddenInput()->label(false); ?>
    <?php } ?>

    <div id="juridical-person-form" class="">

        <fieldset class="data-separator">

            <legend><?= Yii::t('app', 'General'); ?></legend>

            <div class="row">
                <div class="col-sm-4">
                    <?= $form->field($juridicalPerson, 'cnpj')->textInput(['maxlength' => 18])->widget(MaskedInput::className(), ['mask' => '99.999.999/9999-99']) ?>
                </div>

                <div class="col-sm-4">
                    <?= $form->field($juridicalPerson, 'company_name')->textInput(['maxlength' => 255]) ?>
                </div>

                <div class="col-sm-4">
                    <?= $form->field($juridicalPerson, 'trading_name')->textInput(['maxlength' => 255]) ?>
                </div>
            </div>

            <div class="row">

                <div class="col-sm-6">
                    <?= $form->field($juridicalPerson, 'contact_name')->textInput(['maxlength' => 255]) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($juridicalPerson, 'ie')->textInput(['maxlength' => 255]) ?>
                </div>

            </div>
        </fieldset>

    </div>

    <div id="physical-person-form" class="">

        <fieldset class="data-separator">

            <legend><?= Yii::t('app', 'General'); ?></legend>

            <div class="row">

                <div class="col-sm-8">
                    <?= $form->field($physicalPerson, 'name')->textInput(['maxlength' => 255]) ?>
                </div>

                <div class="col-sm-4">
                    <?php
                    if ($physicalPerson->isNewRecord) {
                        echo $form->field($physicalPerson, 'cpf')->textInput(['maxlength' => 14])->widget(MaskedInput::className(), ['mask' => '999.999.999-99']);
                    } else {
                        echo $form->field($physicalPerson, 'cpf')->textInput(['readonly' => true]);
                    }
                    ?>
                </div>

            </div>

        </fieldset>

    </div>


    <div id="business-form" class="">

        <fieldset class="data-separator">

            <legend><?= Yii::t('app', 'Informations'); ?></legend>
            
            <div class="row">
                <div class="col-sm-4">
                    <?= $form->field($business, 'representative_legal')->textInput(['maxlength' => 150]) ?>
                </div>

                <div class="col-sm-4">
                    <?= $form->field($business, 'representative_cpf')->textInput(['maxlength' => 14])->widget(MaskedInput::className(), ['mask' => '999.999.999-99']); ?>
                </div>

                <div class="col-sm-4">
                    <?= $form->field($business, 'economic_activity')->textInput(['maxlength' => 255]) ?>
                </div>
            </div>

            <legend><?= Yii::t('app', 'Address'); ?></legend>
            
            <div class="row">
                <div class="col-sm-4">
                    <?= $form->field($legalPerson, 'email')->textInput(['maxlength' => 255, 'type' => 'email']) ?>
                </div>

                <div class="col-sm-4">
                    <?= $form->field($legalPerson, 'website')->textInput(['maxlength' => 255, 'type' => 'url']) ?>
                </div>

                <div class="col-sm-2">
                    <?= $form->field($legalPerson, 'comercial_phone')->textInput(['maxlength' => 255])->widget(MaskedInput::className(), ['mask' => ['(99) 9999-9999', '(99) 99999-9999']]) ?>
                </div>

                <div class="col-sm-2">
                    <?= $form->field($business, 'whatsapp')->textInput(['maxlength' => 255])->widget(MaskedInput::className(), ['mask' => ['(99) 9999-9999', '(99) 99999-9999']]) ?>
                </div>

            </div>

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($legalPerson, 'address')->textInput(['maxlength' => 255]) ?>
                </div>

                <div class="col-sm-3">
                    <?= $form->field($legalPerson, 'address_complement')->textInput(['maxlength' => 255]) ?>
                </div>

                <div class="col-sm-3">
                    <?= $form->field($legalPerson, 'zip_code')->textInput(['maxlength' => 9])->widget(MaskedInput::className(), ['mask' => '99999-999']) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-4">
                    <?= $form->field($legalPerson, 'district')->textInput(['maxlength' => 255]) ?>
                </div>

                <div class="col-sm-4">
                    <?php
                        $initialValueText = null;
                        if ($legalPerson->city) {
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

                <div class="col-sm-4">
                    <?= $form->field($legalPerson, 'cell_number')->textInput(['maxlength' => 255])->widget(MaskedInput::className(), ['mask' => ['(99) 9999-9999', '(99) 99999-9999']]) ?>
                </div>
            </div>

        </fieldset>

        <div class="form-group">
                <div class="text-sm-right">
                    <?= $form->field($business, 'is_disabled')->checkbox()?>
                    <?= Html::submitButton($business->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $business->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
