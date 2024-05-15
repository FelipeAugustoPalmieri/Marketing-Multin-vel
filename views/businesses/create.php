<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Business */

$this->title = Yii::t('app', 'New Business');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Businesses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="business-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'business' => $business,
        'legalPerson' => $legalPerson,
        'juridicalPerson' => $juridicalPerson,
        'physicalPerson' => $physicalPerson,
    ]) ?>

</div>
