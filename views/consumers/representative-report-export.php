<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\GridView;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use app\widgets\LinkPager;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Consumer;
use app\models\Sale;
use app\widgets\AjaxSelect2;
use yii\web\JsExpression;
use rmrevin\yii\fontawesome\FA;

$this->title = Yii::t('app', 'Points Report');
$this->params['breadcrumbs'][] = $this->title;
?>
<h1 align="center"><?= Html::encode($this->title) ?></h1>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'exportable' => false,
    'showFooter' => false,
    'layout' => '{items}',
    'pager' => ['class' => LinkPager::className()],
    'columns' => [
            [
                'attribute' => 'identifier',
                'value' => function($model) {
                    return $model->identifier;
                }
            ],
            [
                'attribute' => 'name',
                'value' => function($model) {
                    return $model->legalPerson->getName().' - '.$model->legalPerson->cell_number.' - '.$model->legalPerson->email;
                }
            ],
            [
                'header' => Yii::t('app', 'Points'),
                'value' => function($model) use ($modelRepresentativeReport) {
                    return $model->getMonthPoints($modelRepresentativeReport->month, $modelRepresentativeReport->year, $model->id);
                }
            ],
            [
                'header' => Yii::t('app', 'Missing points'),
                'value' => function($model) use ($modelRepresentativeReport)  {

                    if (!$model->plane) {
                        return null;
                    }

                    $monthPoints = $model->getMonthPoints($modelRepresentativeReport->month, $modelRepresentativeReport->year, $model->id);

                    if ($monthPoints >= $model->plane->goal_points){
                        return 'Meta Atingida';
                    }

                    return  $model->plane->goal_points - $monthPoints;
                }
            ],
            [
                'header' => Yii::t('app', 'Indications'),
                'value' => function($model) use ($modelRepresentativeReport) {
                    return $model->getMonthIndications($modelRepresentativeReport->month, $modelRepresentativeReport->year, $model->id);
                }
            ]
        ],
    ]);
?>
