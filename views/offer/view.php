<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Tabs;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Plane */

$this->title = $offer->titulo;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Offer'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="plane-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $offer->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $offer->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    
    <?= Tabs::widget([
        'items' => [
            [
                'label' => Yii::t('app', 'General'),
                'content' => DetailView::widget([
                        'model' => $offer,
                        'attributes' => [
                            'titulo',
                            [
                                'attribute' => Yii::t('app', 'Data Inicial'),
                                'value' => \Yii::$app->formatter->asDatetime(new DateTime($offer->dt_inicial))
                            ],
                            [
                                'attribute' => Yii::t('app', 'Data Final'),
                                'value' => \Yii::$app->formatter->asDatetime(new DateTime($offer->dt_final))
                            ],
                            [
                                'attribute' => Yii::t('app', 'Business'),
                                'value' => $offer->business->legalPerson->name,
                            ],
                        ],
                    ]),
                'active' => (empty($tab) || $tab == 'general'),
            ],
            [
                'label' => Yii::t('app', 'Descrição'),
                'content' => "<hr/>".Html::tag('p', $offer->descricao),
                'active' => (empty($tab) || $tab == 'imagem'),
            ],
            [
                'label' => Yii::t('app', 'Imagem'),
                'content' => "<hr/>".Html::img(Url::home() . $offer->image, ['alt'=> 'Imagem da Oferta', 'class'=>'img-responsive', 'width'=>'350']),
                'active' => (empty($tab) || $tab == 'imagem'),
            ]
        ]
    ]);?>
</div>
