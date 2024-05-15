<?php

use app\widgets\GridView;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Businesses Report');
?>

<div class="informations">
    <?= GridView::widget([
        'dataProvider' => $consumablesDataProvider,
        'filterModel' => null,
        'exportable' => false,
        'pager' => false,
        'columns' => [
            'description',
            [
                'attribute' => 'shared_percentage',
                'value' => function($model) {
                    return is_numeric($model->shared_percentage) ? Yii::$app->formatter->asPercent($model->shared_percentage / 100, 2) : null;
                },
            ],
        ],
    ])?>
</div>

