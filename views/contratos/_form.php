<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->registerCssFile(Url::base() . '/css/summernote/summernote.css', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/js/summernote/summernote.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->registerJsFile(Url::base() . '/js/contratos.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<div class="plane-form">

    <?php $form = ActiveForm::begin(['id'=>'formContratos']); ?>

    <!--<textarea class="summernote"></textarea>-->
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'titulo')->textInput(['maxlength' => 150]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'texto')->textarea(['rows' => '8', 'class'=>'summernote']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'flag_local')->radioList( [1=>'Contrato Investimento', 2 => 'Contrato Consumidor', 3 => 'Contrato Convenio'] ); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'flag_cancel')->dropDownList([0=>"Não",1=>"Sim"], ['class' => 'form-control c-select']); ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php if(!$model->isNewRecord){ ?>
            <?= Html::a(Yii::t('app', 'View'), Url::to(['visualizar', 'id' => $model->id]), ['title' => Yii::t('app', 'View'), 'class'=>'btn btn-success pull-right', 'aria-label' => Yii::t('yii', 'View'), 'target' => '_blank' ]);  ?>
        <?php } ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
