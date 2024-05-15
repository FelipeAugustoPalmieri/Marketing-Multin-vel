<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Consumable */

$this->title = Yii::t('app', 'Create Consumable');
$this->params['breadcrumbs'][] = ['label' => $model->business->name, 'url' => ['businesses/view', 'id' => $model->business->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Consumables'), 'url' => ['businesses/view', 'id' => $model->business->id, 'tab' => 'consumables']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="consumable-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
