<?php

use app\widgets\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\bootstrap\Tabs;
use app\widgets\Alert;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model app\models\Business */

$this->registerJsFile(Url::base() . '/js/businessesview.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->title = $model->getName();
if(!Yii::$app->user->can('salesReport')){
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Businesses'), 'url' => ['index']];
}
$this->params['breadcrumbs'][] = $this->title;

$atributosPessoa = ['type'];
if ($model->isPhysicalPerson()) {
    $atributosPessoa = array_merge($atributosPessoa, [
        'legalPerson.person.name',
        'legalPerson.person.cpf',
        'representative_legal',
        'representative_cpf',
    ]);
} elseif ($model->isJuridicalPerson()) {
    $atributosPessoa = array_merge($atributosPessoa, [
        'legalPerson.person.company_name',
        'legalPerson.person.trading_name',
        'legalPerson.person.contact_name',
        'legalPerson.person.cnpj',
        'legalPerson.person.ie',
        'representative_legal',
        'representative_cpf',
        'economic_activity',
        'whatsapp',
        'legalPerson.comercial_phone',
    ]);
}

$atributosEnderecos = [
    'legalPerson.cell_number',
    'legalPerson.address',
    'legalPerson.address_complement',
    'legalPerson.district',
    [
        'attribute' => Yii::t('app', 'City'),
        'value' => $model->legalPerson->city->name . ' - ' . $model->legalPerson->city->state->abbreviation,
    ],
    'legalPerson.zip_code',
    'legalPerson.website:url',
    'legalPerson.email:email',
];

$atributosOutros = [
    'created_at:datetime',
    'updated_at:datetime',
];
?>
<div class="business-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-sm-12">
            <?= Alert::widget() ?>
        </div>
        <?php if(!Yii::$app->user->can('salesReport') || Yii::$app->user->can('admin') ||  Yii::$app->user->can('manageBusinessUsers')){ ?>
            <div class="col-md-6 text-left">
                <?= Html::a(Yii::t('app', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            </div>
            <div class="col-md-6 text-right">
                <?php if(!$listcontract || $listcontract->is_cancel){
                    echo Html::a(Yii::t('app', 'Get Contract'), '#', ['class' => 'btn btn-primary', 'id'=>'btn-contract', 'data-url'=>  Url::base('http').Url::to(['businesses/contract', 'id' => $model->id])]);
                    if($listcontract && $ContractCancel && $ContractCancel->flag_cancel){
                        echo Html::a(Yii::t('app', 'View Contract Cancel'), ['contract', 'id' => $model->id, 'id_contract'=>$ContractCancel->id], ['class' => 'btn btn-warning btn-cancel', 'target'=>'_blank']);
                    }
                }else if(isset($listcontract)){
                    echo Html::a(Yii::t('app', 'Contract'), ['contract', 'id' => $model->id], ['class' => 'btn btn-primary', 'target'=>'_blank']);
                    if(Yii::$app->user->can('admin')){
                        echo Html::a(Yii::t('app', 'Cancel Contract'), ['cancel-contract', 'id' => $model->id], ['class' => 'btn btn-danger btn-cancel']);
                    }
                } ?>
            </div>
        <?php } ?>
    </div>
    <br>
    <div id="data-view">
        <?= Tabs::widget([
            'items' => [
                [
                    'label' => Yii::t('app', 'General'),
                    'content' => DetailView::widget([
                        'model' => $model,
                        'attributes' => $atributosPessoa,
                    ]),
                    'active' => (empty($tab) || $tab == 'general'),
                ],
                [
                    'label' => Yii::t('app', 'Address'),
                    'content' => DetailView::widget([
                        'model' => $model,
                        'attributes' => $atributosEnderecos,
                    ]),
                    'active' => ($tab == 'address'),
                ],
                [
                    'label' => Yii::t('app', 'Users'),
                    'options' => ['id' => 'users-tab'],
                    'content' => Html::tag(
                            'p',
                            Html::a(
                                Yii::t('app', 'Create User'),
                                ['business-users/create', 'businessId' => $model->id],
                                ['class' => 'btn btn-success']
                            )
                        ) . GridView::widget([
                        'dataProvider' => $usersDataProvider,
                        'filterModel' => null,
                        'pager' => false,
                        'columns' => [
                            'name',
                            'email:email',
                            [
                                'header' => Yii::t('app', 'Permissions'),
                                'value' => function($model) {
                                    $permissions = [Yii::t('app', 'Submit Sales')];
                                    if (Yii::$app->authManager->checkAccess($model->getId(), 'salesReport')) {
                                        $permissions[] = Yii::t('app', 'Sales Report');
                                    }
                                    return implode(', ', $permissions);
                                }
                            ],
                            [
                                'class' => 'app\grid\ActionColumn',
                                'visibleButtons' => ['view' => false, 'update' => true, 'delete' => 'true'],
                                'urlCreator' => function($action, $model, $key, $index) {
                                    return Url::to(['business-users/' . $action, 'id' => $model->id]);
                                },
                            ],
                        ],
                    ]),
                    'active' => ($tab == 'users'),
                ],
                [
                    'label' => Yii::t('app', 'Repasse'),
                    'options' => ['id' => 'consumables-tab'],
                    'content' => Html::tag(
                            'p',
                            Html::a(
                                Yii::t('app', 'Create Consumable'),
                                ['consumable/create', 'businessId' => $model->id],
                                ['class' => 'btn btn-success']
                            )
                        ) . GridView::widget([
                        'dataProvider' => $consumablesDataProvider,
                        'filterModel' => null,
                        'pager' => false,
                        'columns' => [
                            'description',
                            [
                                'attribute' => 'shared_percentage',
                                'value' => function($model) {
                                    return is_numeric($model->shared_percentage) ? Yii::$app->formatter->asPercent($model->shared_percentage / 100, 6) : null;
                                },
                            ],
                            [
                                'class' => 'app\grid\ActionColumn',
                                'urlCreator' => function($action, $model, $key, $index) {
                                    return Url::to(['consumable/' . $action, 'id' => $model->id, 'businessId' => $model->business_id]);
                                },
                            ],
                        ],
                    ]),
                    'active' => ($tab == 'consumables'),
                ],
                [
                    'label' => "Upload Imagem",
                    'content' => $this->render('uploadimagem', ['model' => $model]),
                    'active' => ($tab == 'image'),
                ],
                [
                    'label' => Yii::t('app', 'Others'),
                    'content' => DetailView::widget([
                        'model' => $model,
                        'attributes' => $atributosOutros,
                    ]),
                    'active' => ($tab == 'others'),
                ],
            ]
        ]);
        ?>
    </div>
</div>
<?php Modal::begin([
    'header' => '<h2 class="modal-title">'.Yii::t('app','Get Contract').'</h2>',
    'id'     => 'modal-contract',
    'footer' => Html::a(Yii::t('app', 'Não'), '#', ['class' => 'btn btn-danger float-left', 'id' => 'contract-not']) . ' ' . Html::a(Yii::t('app', 'Sim'), '#', ['class' => 'btn btn-success', 'id' => 'contract-confirm']),
]); ?>
<?= '<h4 class="text-center">'.sprintf(Yii::t('app', 'Please check all agreement data to generate the contract.'), $model->name).'</h4>'; ?>
<?php Modal::end(); ?>