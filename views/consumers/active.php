<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Plane;
use yii\helpers\Html;
use yii\helpers\Url;



$this->title = Yii::t('app', 'Active Consumer') . ': ' . $model->legalPerson->getName();;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Consumers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile(Url::base() . '/js/activeConsumer.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<div class="user-form">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'identifier')->textInput(['maxlength' => 255]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'plane_id')->dropDownList(ArrayHelper::map(Plane::find()->all(), 'id', 'name_plane'),['class' => 'form-control c-select', 'prompt' => Yii::t('app', 'Select Plan')]) ?>
        </div>
    </div>
        <div class="col-sm-12 text-xs-right">
            <?= Html::submitButton($model->isNewRecord ? : Yii::t('app', 'Active'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id'=> "ativarUsuario"]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
