<?php
use app\models\Configuration;
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = Yii::t('app', 'Configurations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="configuration-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'name',
            [
                'attribute' => 'type',
                'filterInputOptions' => ['class' => 'c-select'],
                'filter' => Configuration::getTypes(),
                'value' => function($model) {
                    return $model->getTypeDescription();
                }
            ],
            'value',
            [
                'class' => 'app\grid\ActionColumn',
                'contentOptions' => ['class' => 'text-nowrap'],
                'template' => '{view} {update}'
            ],
        ],
    ]); ?>

</div>
