<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="plane-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name_plane')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'multiplier')->textInput(['type' => 'number', 'step' => 0.01]) ?>

    <?= $form->field($model, 'goal_points')->textInput(['type' => 'number', 'step' => 0.1]) ?>

    <?= $form->field($model, 'value')->textInput(['type' => 'number', 'step' => 0.1]) ?>

    <?= $form->field($model, 'bonus_percentage')->textInput(['type' => 'number', 'step' => 0.1]) ?>

    <?= $form->field($model, 'pay_plane')->textInput(['maxlength' => 255]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
