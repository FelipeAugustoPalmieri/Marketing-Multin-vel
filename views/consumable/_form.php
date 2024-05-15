<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Consumable */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile(Url::base() . '/js/jquery.maskMoney.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/js/consumable.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
(\Yii::$app->devicedetect->isMobile() == false ? $isMobile = 0 : $isMobile = 1);

?>

<div class="consumable-form">

    <?php $form = ActiveForm::begin(['enableClientValidation'=>false]); ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => 255]) ?>

	<?php if(!$isMobile)
        echo $form->field($model, 'shared_percentage')->textInput(['maxlength' => 100, 'data-prefix' => '', 'data-thousands' => '', 'data-decimal' => ',']);
  	else {
        echo $form->field($model, 'shared_percentage')->textInput(['maxlength' => 100, 'placeholder' => '0,00', 'type' => 'number']);
  	}?>

    <?php if(!$isMobile)
        echo $form->field($model, 'shared_percentage_adm')->textInput(['maxlength' => 100, 'data-prefix' => '', 'data-thousands' => '', 'data-decimal' => ',']);
  	else {
        echo $form->field($model, 'shared_percentage_adm')->textInput(['maxlength' => 100, 'placeholder' => '0,00', 'type' => 'number']);
  	}?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script type="text/javascript">
    var isMobile = <?= $isMobile ?>;
</script>