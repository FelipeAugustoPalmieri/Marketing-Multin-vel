<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\SalesRepresentativeCity */

$this->title = Yii::t('app', 'Create Sales Representative City');
$this->params['breadcrumbs'][] = ['label' => $model->salesRepresentative->legalPerson->name, 'url' => ['consumers/view', 'id' => $model->salesRepresentative->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Representative'), 'url' => ['consumers/view', 'id' => $model->salesRepresentative->id, 'tab' => 'representative']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="sales-representative-city-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
