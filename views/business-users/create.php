<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = Yii::t('app', 'Create User');
if(!Yii::$app->user->can('salesReport')){
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Businesses'), 'url' => ['businesses/index']];
}
$this->params['breadcrumbs'][] = ['label' => $business->getName(), 'url' => ['businesses/view', 'id' => $business->id, 'tab' => 'users']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'business' => $business,
    ]) ?>

</div>
