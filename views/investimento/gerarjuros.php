<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Plane */

$this->title = Yii::t('app', 'Gerou com Sucesso');;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Porcentagem'), 'url' => ['porcentagem']];
$this->params['breadcrumbs'][] = ['label' => \Yii::$app->formatter->asDatetime(new DateTime($model->data_referencia),'dd/MM/yyyy'), 'url' => ['preview', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="geroucomsucesso-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <img src="<?= Url::base() ?>/images/sucessofogos.gif" alt="Sucesso Tbest Investimento" />
</div>