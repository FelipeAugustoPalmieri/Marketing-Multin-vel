<?php

use yii\widgets\DetailView;
use yii\helpers\Html;
use rmrevin\yii\fontawesome\FA;

?>

<div class="informations">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
                [
                    'attribute' => 'name',
                    'value' => $model->legalPerson->getName(),
                ],
                [
                    'attribute' => Yii::t('app', 'Cell Phone'),
                    'format' => 'html',
                    'value' => function($model){
                        if(Yii::$app->devicedetect->isMobile() == false){
                            return $model->legalPerson->phoneNumber;
                        }else{
                            return $model->legalPerson->phoneNumber . Html::a(FA::icon('whatsapp'), 'https://api.whatsapp.com/send?phone=55'.preg_replace("/[^0-9]/", "", $model->legalPerson->phoneNumber), ['style' => 'margin-left: 10px;']). Html::a(FA::icon('phone'), 'tel:'.preg_replace("/[^0-9]/", "", $model->legalPerson->phoneNumber), ['style' => 'margin-left: 10px;']);
                        }
                    },
                ],
                [
                    'attribute' => Yii::t('app', 'Email'),
                    'value' => $model->legalPerson->email,
                ],
                [
                    'attribute' => Yii::t('app', 'Address'),
                    'value' => $model->legalPerson->address,
                ],
                [
                    'attribute' => Yii::t('app', 'District'),
                    'value'=> $model->legalPerson->district,
                ],
                [
                    'attribute' => Yii::t('app', 'City'),
                    'value' => $model->legalPerson->city->name . ' - ' . $model->legalPerson->city->state->abbreviation,
                ],
                [
                    'attribute' => Yii::t('app', 'Name Plane'),
                    'value' =>  $model->plane->name_plane,
                ],
                [
                    'attribute' => Yii::t('app', 'Parent Consumer'),
                    'value' => $model->parentConsumer? $model->parentConsumer->legalPerson->name : null,
                ],
        ],
    ]) ?>
</div>
