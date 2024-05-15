<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = Yii::t('app', 'Update User') . ': ' . $model->name;
if(!Yii::$app->user->can('salesReport')){
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Businesses'), 'url' => ['businesses/index']];
}
$this->params['breadcrumbs'][] = ['label' => $business->getName(), 'url' => ['businesses/view', 'id' => $business->id, 'tab' => 'users']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update User');
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'business' => $business->id,
    ]) ?>

</div>
