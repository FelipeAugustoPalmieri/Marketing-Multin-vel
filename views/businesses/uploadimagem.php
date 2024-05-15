<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $business app\models\Business */

$this->title = "Upload da Logo";
$this->registerJsFile(Url::base() . '/dropzone/dropzone.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile(Url::base() . '/dropzone/dropzone.css', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/js/uploadimagem.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<div class="business-update">
    <h1 class="text-center" style="margin-bottom: 20px;"><?= Html::encode($this->title) ?></h1>
    <div class="row"><div class="col-sm-12" id="msg"></div></div>
    <div class="row">
        <div class="<?= (($model->logoempresa)? "col-sm-4" : "hide" ); ?>">
            <img class="img-responsive" id="imagemlogo" src="<?php echo Url::home() . $model->logoempresa; ?>" />
        </div>
        <div class="<?= (($model->logoempresa)? "col-sm-6" : "col-sm-12" ); ?>">
            <?php 
                $form = ActiveForm::begin([
                    'options' => [ 'class' => 'dropzone'], 
                    'id'=>'my-awesome-dropzone'
                ]
            ); 
            ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>