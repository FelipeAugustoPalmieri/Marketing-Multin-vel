<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $business app\models\Business */

$this->title = Yii::t('app', 'Edit Business') . ': ' . $business->getName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Businesses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $business->getName(), 'url' => ['view', 'id' => $business->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edit');
?>
<div class="business-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'business' => $business,
        'legalPerson' => $legalPerson,
        'juridicalPerson' => $juridicalPerson,
        'physicalPerson' => $physicalPerson,
    ]) ?>

</div>
