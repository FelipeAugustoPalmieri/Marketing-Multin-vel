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

$this->registerJsFile(Url::base() . '/js/representativeReport.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/js/tree-modal.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/js/exportPdf.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->title = Yii::t('app', 'Points Report');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="form card card-block">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => Url::to(['consumers/representative-report']),
    ]); ?>
        <div class="row">
            <div class="col-sm-6">
                <?php if (in_array('admin', array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))) || Yii::$app->user->can('receptionist')) {
                    echo $form->field($modelRepresentativeReport, 'consumer_representative_id')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(
                        Consumer::find()->businessRepresentatives()->affiliationPaid()->all(),
                        'id',
                        function($item) {
                            return $item->identifier . ' - ' . $item->legalPerson->name . ' - ' . $item->legalPerson->nationalIdentifier;
                        }
                    ),
                    'theme' => Select2::THEME_BOOTSTRAP,
                    'options' => ['placeholder' => 'Digite nome, CPF ou Código ...'],
                    'pluginOptions' => ['allowClear' => true],
                ]); } else {
                        $consumerUser = Consumer::find()->where(['id' => Yii::$app->user->identity->authenticable_id])->businessRepresentatives()->affiliationPaid()->one();
                        echo $form->field($modelRepresentativeReport, 'consumer_representative_id')->dropDownList(
                            [$consumerUser->id => $consumerUser->legalPerson->name],
                            ['class' => 'form-control c-select']
                        );
                    } ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($modelRepresentativeReport, 'city_id')->dropDownList(['' => Yii::t('app', 'Select Representative')]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-xs-6">
                <?= $form->field($modelRepresentativeReport, 'period')->input('date', ['class' => 'form-control input-datepicker', 'type' => 'month']) ?>
            </div>

            <div class="col-md-4 col-xs-2" style="padding-top: 30px;">
                <?= $form->field($modelRepresentativeReport, 'xinativos')->checkbox(); ?>
            </div>

            <div class="col-md-3 col-xs-4" style="padding-top: 24px;">
                <?= Html::submitButton(Yii::t('app', 'Generate'), ['class' => 'btn btn-primary', 'id' => 'bt-submit']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
</div>

<a class="btn btn-secondary button-red" href="representative-report-export" target="_blank" data-role="export" id="exportButton">Exportar</a>
<?php
    yii\bootstrap\Modal::begin(['id' =>'modal']);
    yii\bootstrap\Modal::end();
?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'exportable' => false,
    'showFooter' => true,
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
                    return $model->legalPerson->getName();
                }
            ],
            [
                'header' => Yii::t('app', 'Born on'),
                'value' => function($model) {
                    return \Yii::$app->formatter->asDatetime($model->legalPerson->person->born_on, 'dd/MM/yyyy');
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
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                    'buttons' => [
                        'informations' => function ($url, $model, $key) {
                        return Html::a(FA::icon('address-book-o'), ['consumers/informations', 'id' => $model->id], ['data-modal' => 'popupModal'] );
                        },
                     ],
                'template' => '{informations}'
            ],
    ],
  ]);
?>

<?php $this->registerJs(
    "$(function() {
        $('[data-modal=\"popupModal\"]').click(function(e) {
            e.preventDefault();
            $('#modal').modal('show').find('.modal-body')
            .load($(this).attr('href'));
        });
    });"
); ?>

<script type="text/javascript">
    var textoSelecione = '<?= Yii::t('app', 'Select Representative'); ?>';
    var textoTodasCidades = '<?= Yii::t('app', 'All'); ?>';
    var citySelected = '<?= $modelRepresentativeReport->city_id; ?>';
</script>
