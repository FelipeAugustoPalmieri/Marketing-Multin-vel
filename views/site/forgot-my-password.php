<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = Yii::t('app', 'Forgot My Password');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-forgot-my-password container">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-sm-4">
                <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'placeholder' => Yii::t('app', 'Email')])->label(false) ?>
            </div>
            <div class="col-sm-4">
                <?= Html::submitButton(Yii::t('app', 'Request New Password'), ['class' => 'btn btn-primary']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
</div>
