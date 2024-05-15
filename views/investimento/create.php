<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Business */

$this->title = Yii::t('app', 'New Porcentagem');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Porcentagem'), 'url' => ['porcentagem']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="porcentagem-create">

<?= $this->render('_form', [
    'model' => $model,
    'planoinvestimento' => $planoinvestimento
]) ?>

</div>