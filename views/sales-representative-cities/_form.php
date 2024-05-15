<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\AjaxSelect2;
use yii\helpers\Url;
use yii\web\JsExpression;
use app\models\Consumer;

?>
<div class="sales-representative-city-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-12">
           <?php
                $initialValueText = null;
                if ($model->city) {
                    $initialValueText = $model->city->name . ' - ' . $model->city->state->abbreviation;
                }
                echo $form->field($model, 'city_id')->widget(AjaxSelect2::classname(), [
                    'ajaxUrl' => Url::to(['api/cities/index']),
                    'ajaxData' => new JsExpression('function(params) { return { CitySearch: {name: params.term}, page: params.page}; }'),
                    'initValueText' => $initialValueText,
                    'templateResult' => new JsExpression('function(city) { return city.name; }'),
                    'templateSelection' => new JsExpression('function (city) { return city.text || city.name; }'),
            ]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
