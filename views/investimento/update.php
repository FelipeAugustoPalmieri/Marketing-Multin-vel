<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $business app\models\Business */

$this->title = Yii::t('app', 'Edit Porcentagem') . ': ' . $model->porcentagem;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Porcentagem'), 'url' => ['porcentagem']];
$this->params['breadcrumbs'][] = ['label' => $model->porcentagem, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edit');
?>
<div class="business-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'planoinvestimento' => $planoinvestimento
    ]) ?>

</div>