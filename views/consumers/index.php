<?php

use app\widgets\LinkPager;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Json;
use app\models\PhysicalPerson;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\ConsumerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Consumers');
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile(Url::base() . '/js/consumersindex.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<div class="consumer-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-sm-6 text-left">
            <?= Html::a(Yii::t('app', 'New Consumer'), ['create'], ['class' => 'btn btn-primary']) ?>
        </div>
        <?php if (in_array('admin', array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)))) { ?>
            <div class="col-sm-6 text-right">
                <?= Html::a(Yii::t('app', 'Reorganizar Rede'), ['rearrange'], ['class' => 'btn btn-danger']) ?>
            </div>
        <?php } ?>
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => LinkPager::className()],
        'columns' => [
            [
                'attribute' => 'identifier',
                'value' => 'identifier',
                'headerOptions' => ['style' => 'width:10%'],
            ],
            [
                'attribute' => 'name',
                'value' => function($model) {
                    return $model->legalPerson->getName();
                }
            ],
            [
                'attribute' => 'phoneNumber',
                'contentOptions' => ['class' => 'text-nowrap'],
                'value' => function($model) {
                    return $model->legalPerson->cell_number;
                }
            ],
            [
                'attribute' => 'email',
                'contentOptions' => ['class' => 'text-nowrap'],
                'value' => function($model) {
                    return $model->legalPerson->email;
                }
            ],
            [
                'attribute' => 'paid_affiliation_fee',
                'filterInputOptions' => ['class' => 'c-select'],
                'format' => 'raw',
                'filter' => [
                    0 => Yii::t('yii', 'No'),
                    1 => Yii::t('yii', 'Yes'),
                ],
                'value' => function($model) {

                    if ($model->paid_affiliation_fee) {
                        return Yii::t('yii', 'Yes');
                    }

                    if (in_array('admin', array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)))) {
                        return Html::a(
                            Yii::t('app', 'Activate'),
                            Url::to(['active', 'id' => $model->id]),
                            [
                                'class' => 'btn btn-secondary btn-sm habilitar-consumer',
                            ]
                        );
                    }

                    return Yii::t('yii', 'No');
                }
            ],
            [
                'class' => 'app\grid\ActionColumn',
                'contentOptions' => ['class' => 'text-nowrap'],
                'template' => '{view} {exportasaas} {update} {delete} {desabilitar}',
                'buttons' => [
                    'desabilitar' => function($url, $model){
                        return  Html::a('<i class="fa fa-thumbs-o-down"></i>', $url, [
                            'title' => Yii::t('app', 'disable consumer'),
                            'class' => 'desabilitar-consumer',
                            'data-id' => $model->id,
                        ]);
                    },
                    'exportasaas' => function($url, $model){
                        return  Html::a('<img src="https://sistema.tbest.com.br/images/icon-asaas.png" width="15">', $url, [
                        'title' => $model->id_asaas ? Yii::t('app', 'consumer is create in asaas') : Yii::t('app', 'export consumer for asaas'),
                            'class' => ((!$model->id_asaas || !$model->id_bling) ? 'exportasaas-consumer' : 'exportasaas disabled'),
                            'data-id' => $model->id,
                        ]);
                    }
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    $url = '';
                    switch($action)
                    {
                        case 'desabilitar':
                            return '#';
                        case 'view':
                            return Url::to(['consumers/view', 'id' => $model->id]);
                        case 'update':
                            return Url::to(['consumers/update', 'id' => $model->id]);
                        case 'delete':
                            return Url::to(['consumers/delete', 'id' => $model->id]);
                    }
                }
            ],
        ],
    ]); ?>

    <?php Modal::begin([ 
        'header' => '<h2></h2>',
        'toggleButton' => false,
        'footer' => '<a href="#" class="btn btn-danger float-left" id="nao-disabled">Não</a><a href="#" class="btn btn-success text-right" id="sim-disabled">Sim</a>'
    ]); 

    Modal::end(); ?>
</div>