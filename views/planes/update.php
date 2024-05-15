<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Plane */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Plane',
]) . ' ' . $model->name_plane;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Planes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name_plane, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="plane-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
