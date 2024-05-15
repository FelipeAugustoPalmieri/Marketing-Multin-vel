<?php

use app\widgets\LinkPager;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;


/* @var $this yii\web\View */
/* @var $searchModel app\models\search\ConsumerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Plane Investiment');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="consumer-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a(Yii::t('app', 'Create Plane'), ['create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => LinkPager::className()],
        'columns' => [
            [
                'attribute' => 'id',
                'value' => 'id',
            ],
            [
                'attribute' => 'nome',
                'value' => 'nome'
            ],
            [
                'attribute' => 'quantidade_meses',
                'value' => 'quantidade_meses'
            ],
            [
                'class' => 'app\grid\ActionColumn',
                'contentOptions' => ['class' => 'text-nowrap'],
                'template' => '{update} {delete}'
            ]
        ],
    ]); ?>

</div>
