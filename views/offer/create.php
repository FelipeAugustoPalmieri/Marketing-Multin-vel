<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $consumer app\models\Consumer */
/* @var $legalPerson app\models\LegalPerson */
/* @var $physicalPerson app\models\PhysicalPerson */

$this->title = Yii::t('app', 'Create Offer');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Offer'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', ['offer' => $offer ]); ?>

</div>