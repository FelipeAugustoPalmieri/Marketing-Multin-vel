<?php

use app\widgets\LinkPager;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\widgets\Alert;


/* @var $this yii\web\View */
/* @var $searchModel app\models\search\ConsumerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Contratos');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="consumer-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a(Yii::t('app', 'Cadastro Contrato'), ['create'], ['class' => 'btn btn-primary']) ?>
    </p>
    <div class="row">
        <div class="col-sm-12">
            <?= Alert::widget() ?>
        </div>
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => LinkPager::className()],
        'columns' => [
            [
                'attribute' => 'id',
                'value' => 'id',
                'headerOptions'=>['style' => 'width:10%'],
            ],
            [
                'attribute' => 'titulo',
                'value' => 'titulo'
            ],
            [
                'attribute' => 'flag_cancel',
                'value' => function($model){
                    return $model->flag_cancel == 0 ? "Não" : "Sim";
                }
            ],
            [
                'attribute' => 'created_at',
                'value' => function($model) {
                    return \Yii::$app->formatter->asDate($model->created_at,'dd/MM/yyyy');
                },
            ],
            [
                'attribute' => 'updated_at',
                'value' => function($model) {
                    return \Yii::$app->formatter->asDate($model->updated_at,'dd/MM/yyyy');
                },
            ],
            [
                'attribute' => 'flag_local',
                'value' => function($model) {
                    return $model->getFlagLocal($model->flag_local);
                },
            ],
            [
                'class' => 'app\grid\ActionColumn',
                'contentOptions' => ['class' => 'text-nowrap'],
                'template' => '{update} {delete}'
            ]
        ],
    ]); ?>

</div>
