<?php
use app\models\PorcentagemInvestimento;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\PlanoInvestimento;

$this->title = Yii::t('app', 'Porcentagem');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="configuration-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a(Yii::t('app', 'Create Porcentagem'), ['create-porcentagem'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Migrar Investimento'), ['migrar-investimento'], ['class' => 'btn btn-danger pull-right']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dados,
        'filterModel' => $model,
        'columns' => [
            [
                'attribute' => 'porcentagem',
                'headerOptions'=>['style' => 'width:20%'],
                'value' => function($model){
                    return \Yii::$app->formatter->asPercent($model->porcentagem/100, 2);
                }
            ],
            [
                'attribute' => 'data_referencia',
                'value' => function($model) {
                    return \Yii::$app->formatter->asDate($model->data_referencia,'dd/MM/yyyy');
                },
            ],
            [
                'attribute' => 'plane_investiment_id',
                'filter' => ArrayHelper::map(PlanoInvestimento::find()->all(), 'id', 'nome'),
                'value' => function($model) {
                    return ($model->plane_investiment_id) ? $model->planeInvestimento->nome : 'Sem Plano Selecionado';
                },
            ],
            [
                'class' => 'app\grid\ActionColumn',
                'contentOptions' => ['class' => 'text-nowrap'],
                'template' => '{update} {delete} {gerar}',
                'buttons' => [
                    'gerar' => function($url, $model){
                        return  Html::a('<i class="fa fa-trademark"></i>', $url, [
                            'title' => Yii::t('app', 'lead-delete'),
                            'class' => 'btn btn-success pull-right', 
                        ]);
                    }
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'gerar') {
                        return Url::to(['investimento/preview', 'id' => $model->id]);
                    }else if ($action === 'update'){
                        return Url::to(['investimento/update', 'id' => $model->id]);
                    }
                }
            ],
        ],
    ]); ?>

</div>
