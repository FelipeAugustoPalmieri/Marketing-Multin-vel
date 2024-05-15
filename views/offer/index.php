<?php
    use yii\helpers\Url;
    use yii\helpers\Html;
    use yii\grid\GridView;
    use app\widgets\LinkPager;

    $this->title = Yii::t('app', 'Ofertas');
    $this->params['breadcrumbs'][] = $this->title;
    $this->registerJsFile(Url::base() . '/js/offer.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<div class="consumer-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-sm-6 text-left">
            <?= Html::a(Yii::t('app', 'New Offer'), ['create'], ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <hr>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => LinkPager::className()],
        'columns' => [
            [
                'attribute' => 'titulo',
            ],
            [
                'attribute' => 'dt_inicial',
                'format' => 'raw',
                'value' => function($model) {
                    return \Yii::$app->formatter->asDatetime(new DateTime($model->dt_inicial));
                }
            ],
            [
                'attribute' => 'dt_final',
                'format' => 'raw',
                'value' => function($model) {
                    return \Yii::$app->formatter->asDatetime(new DateTime($model->dt_final));
                }
            ],
            [
                'attribute' => 'nomeconvenio',
                'value' => function($model) {
                    return $model->business->getName();
                }
            ],
            [
                'class' => 'app\grid\ActionColumn',
                'contentOptions' => ['class' => 'text-nowrap'],
                'template' => '{view} {update}'
            ],
        ],
    ]); ?>
</div>