<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = Yii::t('app', 'Qualifications');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qualification-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Qualification'), ['create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'description',
             [
                'attribute' => 'gain_percentage',
                'value' => function($model) {
                    return is_numeric($model->gain_percentage) ? Yii::$app->formatter->asPercent($model->gain_percentage / 100, 2) : null;
                },
            ],
            'position',
            'completed_levels',
            'points',
            'register_network_sale:boolean',
           [
                'class' => 'app\grid\ActionColumn',
                'contentOptions' => ['class' => 'text-nowrap'],
            ],
        ],
    ]); ?>

</div>
