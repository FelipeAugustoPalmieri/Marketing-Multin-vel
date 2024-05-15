<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use \yii\bootstrap\BootstrapAsset;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile(Url::base() . '/css/site/login.css', ['depends' => [BootstrapAsset::className()]]);
$this->registerJsFile(Url::base() . '/js/login.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<div class="site-login">
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'enableClientValidation' => false,
        'options' => ['role' => 'login'],
    ]); ?>
        <p><img width="180px" src="<?= Url::base() ?>/images/newlogotipo-tbest-login.png" alt="TBest"></p>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => Yii::t('app', 'Username')])->label(false) ?>
        <?= $form->field($model, 'password')->passwordInput(['placeholder' => Yii::t('app', 'Password')])->label(false) ?>

        <?= Html::submitButton('Login', ['class' => 'btn btn-primary btn-block', 'name' => 'login-button']) ?>

        <p class="text-xs-center"><?= Html::a(Yii::t('app', 'I forgot my password.'), Url::to(['site/forgot-my-password']), ['class' => 'forgotmypassword']) ?></p>
        <i id="carregamento" class="fa fa-spinner hide fa-pulse fa-3x fa-fw margin-bottom"></i>

    <?php ActiveForm::end(); ?>
</div>
