<?php

use app\widgets\LinkPager;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use rmrevin\yii\fontawesome\FA;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */



$this->title = 'Sua Nova Senha';
?>
<div class="Jedax@2024">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>Sua nova senha é: <?= Html::encode($password) ?></p>
</div>
$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="alert alert-info" role="alert">
        <i class="fa fa-info-circle" aria-hidden="true"></i>
        <?= Yii::t('app', 'Consumers users are automatically created once they pay the affiliation fee.') ?>
        <br />
        <?= Yii::t('app', 'Business users can be manually created from the business view details.') ?>
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => LinkPager::className()],
        'columns' => [
            [
                'attribute' => 'authenticable_type',
                'filterInputOptions' => ['class' => 'c-select'],
                'filter' => [
                    'Business' => Yii::t('app', 'Business'),
                    'Consumer' => Yii::t('app', 'Consumer'),
                ],
                'value' => function($model) {
                    if ($model->authenticable_type) {
                        return Yii::t('app', $model->authenticable_type);
                    }
                    return Yii::t('app', 'Admin');
                }
            ],
            'login',
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function($model) {
                    if ($model->authenticable_type) {
                        $route = $model->authenticable_type == 'Consumer' ? 'consumers' : 'businesses';
                        return Html::a(
                            Html::encode($model->name),
                            Url::to([$route . '/view', 'id' => $model->authenticable_id])
                        );
                    }
                    return $model->name;
                }
            ],
            'email:email',
            [
                'class' => 'app\grid\ActionColumn',
                'contentOptions' => ['class' => 'text-nowrap'],
                'template' => '{email}'
            ],
        ],
    ]); ?>

</div>
