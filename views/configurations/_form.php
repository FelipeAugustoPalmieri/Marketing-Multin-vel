<?php

use app\models\Configuration;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<div class="configuration-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'type')->dropDownList(Configuration::getTypes(), ['class' => 'form-control c-select']) ?>
        </div>
    </div>

    <?= $form->field($model, 'value')->textInput(['maxlength' => 255]) ?>

    <div class="form-group text-xs-right">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
