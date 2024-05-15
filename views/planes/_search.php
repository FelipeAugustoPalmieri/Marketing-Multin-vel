<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="plane-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name_plane') ?>

    <?= $form->field($model, 'multiplier') ?>

    <?= $form->field($model, 'goal_points') ?>

    <?= $form->field($model, 'value') ?>

    <?= $form->field($model, 'bonus_percentage') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
