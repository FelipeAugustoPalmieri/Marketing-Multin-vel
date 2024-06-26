<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Plane */

$this->title = Yii::t('app', 'Cadastro Contrato');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contratos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="plane-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
