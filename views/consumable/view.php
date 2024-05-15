<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Consumable */

$this->title = $model->description;
$this->params['breadcrumbs'][] = ['label' => $model->business->name, 'url' => ['businesses/view', 'id' => $model->business->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Consumables'), 'url' => ['businesses/view', 'id' => $model->business->id, 'tab' => 'consumables']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="consumable-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create'), ['create', 'businessId' => $model->business->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id, 'businessId' => $model->business->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id, 'businessId' => $model->business->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'created_at:datetime',
            'updated_at:datetime',
            'description',
            [
            'attribute' => 'shared_percentage',
            'value' => is_numeric($model->shared_percentage) ? Yii::$app->formatter->asPercent($model->shared_percentage / 100, 6) : null,
            ],
            [
            'attribute' => 'shared_percentage_adm',
            'value' => is_numeric($model->shared_percentage_adm) ? Yii::$app->formatter->asPercent($model->shared_percentage_adm / 100, 6) : null,
            ],
        ],
    ]) ?>

</div>
