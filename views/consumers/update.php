<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $consumer app\models\Consumer */
/* @var $legalPerson app\models\LegalPerson */
/* @var $physicalPerson app\models\PhysicalPerson */

$this->title = Yii::t('app', 'Edit Consumer') . ': ' . $consumer->legalPerson->getName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Consumers'), 'url' => Yii::$app->user->can('admin') ? ['index'] : ''];
$this->params['breadcrumbs'][] = ['label' => $consumer->legalPerson->getName(), 'url' => ['view', 'id' => $consumer->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edit');
?>
<div class="consumer-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'consumer' => $consumer,
        'plane' => $plane,
        'planoinvestimento' => $planoinvestimento,
        'legalPerson' => $legalPerson,
        'physicalPerson' => $physicalPerson,
    ]) ?>

</div>
