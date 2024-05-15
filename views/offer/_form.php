<?php

use app\widgets\Alert;
use app\models\Occupation;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;
use yii\helpers\BaseHtml;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use app\models\Business;
use yii\helpers\ArrayHelper;

$this->registerJsFile(Url::base() . '/dropzone/dropzone.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile(Url::base() . '/dropzone/dropzone.css', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Url::base() . '/js/offer.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$roleVerifier = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
?>
<div class="offer-form">
    <?php $form = ActiveForm::begin([]); ?>
    <fieldset class="data-separator">
        <div class="row">
            <div class="col-sm-12">
                <?= Alert::widget() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($offer, 'convenio_id')->widget(Select2::classname(), [
                    'data' => !Yii::$app->user->can('admin') ?
                        ArrayHelper::map(
                            Business::find()->where("id = ".(Yii::$app->user->can('receptionist') ? 1 : $offer->convenio_id ))->all(),
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
                <?= $form->field($offer, 'titulo')->textInput(['maxlength' => 100]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($offer, 'dt_inicial')->textInput(['maxlength' => 10])->widget(MaskedInput::className(), ['mask' => '99/99/9999 99:99:99']) ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($offer, 'dt_final')->textInput(['maxlength' => 10])->widget(MaskedInput::className(), ['mask' => '99/99/9999 99:99:99']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <?= $form->field($offer, 'descricao')->textarea(['rows' => '8']) ?>
            </div>
        </div>
        <div class="row"><div class="col-sm-12" id="msg"></div></div>
        <div class="row">
            <div class="<?= (($offer->image)? "col-sm-4" : "hide" ); ?>">
                <img class="img-responsive" id="imagemoferta" src="<?php echo Url::home() . $offer->image; ?>" />
            </div>
            <div class="<?= (($offer->image)? "col-sm-8" : "col-sm-12" ); ?>">
                <div class="dropzone" id="my-awesome-dropzone">
                </div>
                <input type="hidden" id="offer-image" class="form-control" name="image" value="" />
            </div>
        </div>
    </fieldset>
    <div class="form-group">
        <div class="text-sm-right">
            <?= Html::submitButton($offer->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $offer->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => 'btnCadastrar']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>