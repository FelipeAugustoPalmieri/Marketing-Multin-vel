<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Plane */

$this->title = Yii::t('app', 'Create Plane');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Planes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="plane-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
