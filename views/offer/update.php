<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Plane */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => Yii::t('app', 'Offer'),
]) . ' ' . $offer->titulo;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Offer'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $offer->titulo, 'url' => ['view', 'id' => $offer->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="plane-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'offer' => $offer,
    ]) ?>

</div>
