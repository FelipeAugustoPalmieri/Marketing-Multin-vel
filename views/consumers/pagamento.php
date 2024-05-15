<?php
use yii\helpers\Url;
use yii\helpers\Html;
use app\widgets\Alert;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('app', 'Plane pay');
$this->registerJsFile(Url::base() . '/js/cadastro-fora.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<div class="container">
    <h1 class="text-center"><?= Html::encode($this->title) ?></h1> 
    <div class="row">
        <div class="col-sm-12">
            <?= Alert::widget() ?>
        </div>
    </div>
    <div class="row featurette" style="margin-bottom: 30px;">
        <div class="col-md-12 text-center">
            <h2 class="featurette-heading">Bem vindo <?php echo $consumer->legalPerson->name; ?>. <span class="text-muted">Você está fazendo parte agora do grupo seleto da tbest.</span></h2>
        </div>
    </div>
    <div class="row">
        <?php $form = ActiveForm::begin(['action' => Url::to(['consumers/processar-pagamento'])]); ?>
            <div class="col-sm-4 text-center">
                <img class="img-circle" src="<?= Url::base() ?>/images/titulo_basico.jpeg" alt="Generic placeholder image" width="200" height="200">
                <h2>Plano Start</h2>
                <div class="opcoes-planos">
                    <p>Kite de Produtos Tbest</p>
                </div>
                <div class="forma-pagamento">
                    <?= $form->field($faturamento, 'plane_id')->hiddenInput(['value'=> 1])->label(false); ?>
                    <?= $form->field($faturamento, 'consumer_id')->hiddenInput(['value'=> $codigo])->label(false); ?>
                </div>
                <p>
                    <?= Html::submitButton(Yii::t('app', 'in commerce'), ['class' => 'btn btn-success']); ?>
                </p>
            </div>
        <?php ActiveForm::end(); ?>
        <?php $form = ActiveForm::begin(['action' => Url::to(['consumers/processar-pagamento'])]); ?>
            <div class="col-sm-4 text-center">
                <img class="img-circle" src="<?= Url::base() ?>/images/titulo_premium.jpeg" alt="Generic placeholder image" width="200" height="200">
                <h2>Plano Way</h2>
                <div class="opcoes-planos">
                    <p>Kit de Produtos Tbest</p>
                </div>
                <div class="forma-pagamento">
                    <?= $form->field($faturamento, 'plane_id')->hiddenInput(['value'=> 3])->label(false); ?>
                    <?= $form->field($faturamento, 'consumer_id')->hiddenInput(['value'=> $codigo])->label(false); ?>
                </div>
                <p>
                    <?= Html::submitButton(Yii::t('app', 'in commerce'), ['class' => 'btn btn-success']) ?>
                </p>
            </div>
        <?php ActiveForm::end(); ?>
        <?php $form = ActiveForm::begin(['action' => Url::to(['consumers/processar-pagamento'])]); ?>
            <div class="col-sm-4 text-center">
                <img class="img-circle" src="<?= Url::base() ?>/images/titulo_diamante.jpeg" alt="Generic placeholder image" width="200" height="200">
                <h2>Plano Top</h2>
                <div class="opcoes-planos">
                    <p>Curso AMA Mente Milionária</p>
                    <p>com Mentoria e Certificado</p>
                </div>
                <div class="forma-pagamento">
                    <?= $form->field($faturamento, 'plane_id')->hiddenInput(['value'=> 2])->label(false); ?>
                    <?= $form->field($faturamento, 'consumer_id')->hiddenInput(['value'=> $codigo])->label(false); ?>
                </div>
                <p>
                    <?= Html::submitButton(Yii::t('app', 'in commerce'), ['class' => 'btn btn-success']) ?>
                </p>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>