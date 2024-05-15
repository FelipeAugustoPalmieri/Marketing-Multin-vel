<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\bootstrap\Tabs;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Consumer */

$this->title = $model->legalPerson->getName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Consumers'), 'url' => Yii::$app->user->can('admin') ? ['index'] : ''];
$this->params['breadcrumbs'][] = $this->title;
$isAdmin = in_array('admin', array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)));

$atributosPessoa = [
    'identifier',
    'legalPerson.person.name',
    'legalPerson.person.cpf',
    'legalPerson.person.rg',
    'legalPerson.person.issuing_body',
    'legalPerson.person.pis',
    'legalPerson.person.nationality',
    [
        'attribute' => Yii::t('app', 'Occupation'),
        'value' => $model->legalPerson->person->occupation ? $model->legalPerson->person->occupation->name : null,
    ],
    'legalPerson.person.born_on:date',
    'legalPerson.person.marital_status',
];

$atributosConjuge = [];
if ($model->legalPerson->person->hasPartner()) {
    $atributosConjuge = [
        'legalPerson.person.partner_name',
        'legalPerson.person.partner_born_on:date',
        'legalPerson.person.partner_phone_number',
        'legalPerson.person.partner_cpf',
        'legalPerson.person.partner_rg',
        'legalPerson.person.partner_issuing_body',
    ];
}

$atributosEconomicos = [
    [
        'attribute' => Yii::t('app', 'Sponsor Consumer'),
        'format' => 'raw',
        'value' => $model->sponsorConsumer ? Html::a(
            Html::encode($model->sponsorConsumer->legalPerson->name),
            Url::to(['view', 'id' => $model->sponsorConsumer->id])
        ) : null,
    ],
    [
        'attribute' => Yii::t('app', 'Parent Consumer'),
        'format' => 'raw',
        'value' => $model->parentConsumer ? Html::a(
            Html::encode($model->parentConsumer->legalPerson->name),
            Url::to(['view', 'id' => $model->parentConsumer->id])
        ) : null,
    ],
    'bank_name',
    'bank_number',
    'bank_agency',
    'operation',
    'bank_account',
    'is_business_representative:boolean',
    'paid_affiliation_fee:boolean',
    'plane.name_plane',
];

$atributosEnderecos = [
    'legalPerson.website:url',
    'legalPerson.email:email',
    'legalPerson.cell_number',
    'legalPerson.home_phone',
    'legalPerson.comercial_phone',
    'legalPerson.address',
    'legalPerson.address_complement',
    'legalPerson.district',
    [
        'attribute' => Yii::t('app', 'City'),
        'value' => $model->legalPerson->city->name . ' - ' . $model->legalPerson->city->state->abbreviation,
    ],
    'legalPerson.zip_code',
];

$atributosOutros = [
    'created_at:datetime',
    'updated_at:datetime',
];

$items = [];

$items[] = [
    'label' => Yii::t('app', 'General'),
    'content' => DetailView::widget([
        'model' => $model,
        'attributes' => $atributosPessoa,
    ]),
    'active' => (empty($tab) || $tab == 'general'),
];

if ($model->legalPerson->person->hasPartner()) {
    $items[] = [
        'label' => Yii::t('app', 'Partner'),
        'content' => DetailView::widget([
            'model' => $model,
            'attributes' => $atributosConjuge,
        ]),
        'active' => false
    ];
}

$items[] = [
    'label' => Yii::t('app', 'Address'),
    'content' => DetailView::widget([
        'model' => $model,
        'attributes' => $atributosEnderecos,
    ]),
    'active' => false
];

$items[] = [
    'label' => Yii::t('app', 'Business Information'),
    'content' => DetailView::widget([
        'model' => $model,
        'attributes' => $atributosEconomicos,
    ]),
    'active' => false
];

if ($model->getChildrenConsumers()->count() > 0) {
    $items[] = [
        'label' => Yii::t('app', 'Associated Consumers'),
        'content' => GridView::widget([
            'dataProvider' => new yii\data\ActiveDataProvider([
                'query' => $model->getChildrenConsumers(),
                'pagination' => false,
            ]),
            'filterModel' => null,
            'pager' => false,
            'columns' => [
                'identifier',
                [
                    'attribute' => 'name',
                    'value' => function($model) {
                        return $model->legalPerson->getName();
                    }
                ],
                [
                    'class' => 'app\grid\ActionColumn',
                    'contentOptions' => ['class' => 'text-nowrap'],
                    'template' => '{view}',
                    'options' => ['width' => '5%'],
                ],
            ],
        ]),
        'active' => false
    ];
}

if ($model->is_business_representative == true) {
    $items[] = [
        'options' => ['id' => 'representative-tab'],
        'label' => Yii::t('app', 'Representative'),
        'content' => Html::tag(
            'p',
            ($isAdmin ? Html::a(
                Yii::t('app', 'Add City'),
                ['sales-representative-cities/create', 'consumerId' => $model->id],
                ['class' => 'btn btn-success']
            ) : '')
        ) . GridView::widget([
            'dataProvider' => $salesRepresentativeCityDataProvider,
            'filterModel' => null,
            'pager' => false,
            'columns' => [
                [
                    'attribute' => Yii::t('app', 'City ID'),
                    'value' => function($model) {
                        return $model->city->name . ' - ' . $model->city->state->abbreviation;
                    },
                ],
                [
                    'class' => 'app\grid\ActionColumn',
                    'visible' => (Yii::$app->user->can('admin')),
                    'visibleButtons' => ['view' => false, 'update' => false, 'delete' => true],
                    'urlCreator' => function($action, $model, $key, $index) {
                        return Url::to(['sales-representative-cities/' . $action, 'id' => $model->id, 'consumerId' => $model->sales_representative_id]);
                    },
                ],
            ],
        ]),
        'active' => ($tab == 'representative'),
    ];
}

$items[] = [
    'label' => Yii::t('app', 'Others'),
    'content' => DetailView::widget([
        'model' => $model,
        'attributes' => $atributosOutros,
    ]),
    'active' => false
];

?>

<div class="consumer-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($isAdmin || $model->id == Yii::$app->user->getIdentity()->consumer->id) : ?>
        <?= Html::a(Yii::t('app', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?php endif; ?>

    <?php if ($isAdmin) : ?>

        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure?'),
                'method' => 'post',
            ],
        ]) ?>
    <?php endif; ?>
    <div id="data-view">
        <?= Tabs::widget(['items' => $items]); ?>
    </div>
</div>