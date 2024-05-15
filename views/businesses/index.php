<?php
use app\models\LegalPerson;
use app\widgets\LinkPager;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Business;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\BusinessSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Businesses');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="business-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a(Yii::t('app', 'New Business'), ['create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,

        'pager' => ['class' => LinkPager::className()],
        'columns' => [
            [
                'attribute' => 'name',
                'value' => function($model) {
                    return $model->getName();
                }
            ],
            [
                'attribute' => 'is_disabled',
                'filterInputOptions' => ['class' => 'c-select'],
                'filter' => Business::getDisabled(),
                'value' => function($model) {
                    if($model->is_disabled == TRUE){
                        return Yii::t('app' , 'Yes');
                    } else {
                        return Yii::t('app' , 'No');
                    }
                }
            ],
            [
                'attribute' => 'economic_activity',
                'value' => function($model) {
                    return $model->getEconomicActivity();
                }
            ],
            [
                'attribute' => 'cell_number',
                'value' => function($model) {
                    return $model->getPhoneNumber();
                }
            ],
            [
                'class' => 'app\grid\ActionColumn',
                'contentOptions' => ['class' => 'text-nowrap'],
                'template' => '{view} {update} {delete}'
            ],
        ],
    ]); ?>

</div>
