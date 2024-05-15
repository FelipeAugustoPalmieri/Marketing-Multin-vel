<?php
use app\widgets\Alert;
use app\widgets\AjaxSelect2;
use yii\web\JsExpression;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\money\MaskMoney;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Consumer;
use app\models\search\ConsumerSearch;
use app\models\Business;
use yii\helpers\Url;

$this->registerJsFile(Url::base() . '/js/jquery.maskMoney.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/js/sales.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
(\Yii::$app->devicedetect->isMobile() == false ? $isMobile = 0 : $isMobile = 1);

?>

<div class="sale-form">
    <?php $form = ActiveForm::begin(['enableClientValidation'=>false]); ?>

    <div class="row">
        <div class="col-sm-12">
            <?= Alert::widget() ?>
        </div>
        <div class="col-sm-12">
            <?= $form->field($model, 'consumer_id')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(
                    Consumer::find()->Where(['>','identifier', 0])->orderBy(['identifier'=>SORT_ASC])->all(),
                    'id',
                    function($person) {
                        return $person->identifier." - ".$person->legalPerson->name." - ".$person->legalPerson->person->cpf;
                    }
                ),
                'theme' => Select2::THEME_BOOTSTRAP,
                'readonly' => true,
                'options' => ['placeholder' => 'Digite nome, CPF ou Código ...'],
                'pluginOptions' => ['allowClear' => true],
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'business_id')->widget(Select2::classname(), [
                'data' => !Yii::$app->user->can('admin') ?
                    ArrayHelper::map(
                        Business::find()->where("id = ".(Yii::$app->user->can('receptionist') ? 1 : $model->business_id ))->all(),
                        'id',
                        function($item) {
                            return $item->legalPerson->name . ' - ' . $item->legalPerson->nationalIdentifier;
                        }
                    ) :
                    ArrayHelper::map(
                        Business::find()->where("is_disabled = FALSE")->all(),
                        'id',
                        function($item) {
                            return $item->legalPerson->name . ' - ' . $item->legalPerson->nationalIdentifier;
                        }
                    ),
                'theme' => Select2::THEME_BOOTSTRAP,
                'readonly' => !Yii::$app->user->can('admin') ? true : false,
                'options' => ['placeholder' => 'Digite nome ou CPF/CNPJ ...'],
                'pluginOptions' => ['allowClear' => true],
            ]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'consumable_id')->dropDownList(['' => Yii::t('app', 'Select Business')]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'invoice_code')->textInput(['maxlength' => 255]) ?>
        </div>
        <div class="col-sm-6">
            <?php if(!$isMobile)
                      echo $form->field($model, 'total')->textInput(['maxlength' => 255, 'data-prefix' => 'R$ ', 'data-thousands' => '', 'data-decimal' => ',']);
                  else {
                      echo $form->field($model, 'total')->textInput(['maxlength' => 255, 'placeholder' => 'R$ 0,00', 'type' => 'number']);
                  }?>
        </div>
    </div>
    <div class="row hide" id="vendedorid">
        <div class="col-sm-12">
            <?= $form->field($model, 'consumer_sale_id')->widget(AjaxSelect2::classname(), [
                'pluginOptions' => ['allowClear' => true],
                'ajaxUrl' => Url::to(['api/consumers/index']),
                'ajaxData' => new JsExpression('function(params) {
                    return {
                        ConsumerSearch: {
                            wildCard: params.term,
                            affiliationPaid: 1,
                        },
                        page: params.page
                    };
                }'),
                'templateResult' => new JsExpression('function(person) {
                    return person.identifier + " - " + person.name + " - " + person.national_identifier;
                }'),
                'templateSelection' => new JsExpression('function(person) { return person.identifier === undefined ? "Digite nome, CPF ou Código ..." : person.identifier + " - " + person.name + " - " + person.national_identifier; }'),
                ]); 
            ?>  
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    var textoSelecione = '<?= Yii::t('app', 'Select Business'); ?>';
    var textoSelecioneUm = '<?= Yii::t('app', 'Select option'); ?>';
    var isMobile = <?= $isMobile ?>;
</script>
