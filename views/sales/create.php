<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Sale */

$this->title = Yii::t('app', 'Create Sale');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sale-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <?php if($mostrarpopup){ ?>
        <div class="modal fade" tabindex="-1" id="modalCadastroConvenio" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Cadastrar Detalhes para Aplicativo</h4>
                    </div>
                    <div class="modal-body">
                        <p>Por favor convênio cadastre seus dados para ser referenciado no Aplicativo.</p>
                        <div id="mensagemerro" class="hidden">
                            <hr/>
                            <span class="alert alert-danger">Por favor é importante que coloque as informações a baixo.</span>
                            <hr/>
                        </div>
                        <form>
                            <div class="form-group">
                                <label for="whatsapp">Número WhatsApp</label>
                                <input type="tel" class="form-control" id="whatsapp" placeholder="Número WhatsApp">
                            </div>
                            <div class="form-group">
                                <label for="comercial">Número Comercial</label>
                                <input type="tel" class="form-control" id="comercial" placeholder="Número Comercial">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="buttoncancel" data-dismiss="modal">Deixar para Depois</button>
                        <button type="button" id="salvarComplemento" class="btn btn-success">Salvar</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    <?php } ?>

</div>
