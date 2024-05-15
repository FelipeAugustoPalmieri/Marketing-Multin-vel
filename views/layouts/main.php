<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\models\Consumer;
use app\models\Investimento;
use yii\web\UrlManager;

AppAsset::register($this);
$roles = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->id));
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= $this->render('_favicons') ?>
    <?= Html::csrfMetaTags() ?>
    <title>Jedax</title>
    <?php $this->head() ?>
    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-129668625-2"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-129668625-2');
    </script>
    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
            var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
            s1.async=true;
            s1.src='https://embed.tawk.to/5c17cfa97a79fc1bddf14a0f/default';
            s1.charset='UTF-8';
            s1.setAttribute('crossorigin','*');
            s0.parentNode.insertBefore(s1,s0);
        })();
    </script>
    <!--End of Tawk.to Script-->
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap">
    <div class="row">
        <?php if(isset(Yii::$app->user->getIdentity()->name)){ ?>
            <div class="col-md-3 remove-padding-right removemobilepad">
                <nav id="navbar" class="navbar navbar-default navbarnovo" role="navigation">
                    <div class="row">
                        <div class="col-xs-12 navbar-header">
                            <a class="navbar-brand pull-left" style="min-height: 110px;" href="<?= Url::base() ?>/"><img width="100px" src="<?= Url::base() ?>/images/newlogo-tbest2.png" alt="Jedax"></a>
                            <button type="button" class="bg-primary pull-right navbar-toggler visible-xs-block collapsed" data-toggle="collapse" data-target="#collapsingNavbar" style="margin-top: 45px;margin-right: 20px;">
                                <span class="sr-only">Toggle navigation</span>
                                ☰
                            </button>
                        </div>
                    </div>
                    <div id="collapsingNavbar" class="collapse navbar-collapse">
                        <div id="accordion" role="tablist" aria-multiselectable="true" >
                            <?php if (Yii::$app->user->can('viewMyProfile')) : ?>
                                <div class="panel">
                                    <div class="panel-heading" role="tab" id="headingFife">
                                        <a class="linkmenu" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseExample6" aria-expanded="true" aria-controls="collapseExample6">
                                            <h6 class="sidebar-title"><?= Yii::t('app', 'Profile') ?></h6>
                                        </a>
                                    </div>
                                    <div class="collapse" id="collapseExample6" role="tabpanel" aria-labelledby="headingFife">
                                        <?= Nav::widget([
                                            'options' => ['class' => 'nav-stacked nav-pills'],
                                            'items' => [
                                                [
                                                    'label' => Yii::t('app', 'Start'),
                                                    'url' => ['/'],
                                                    'visible' => Yii::$app->user->can('viewMyProfile'),
                                                ],
                                                [
                                                    'label' => Yii::t('app', 'My Data'),
                                                    'url' => ['consumers/view?id=' . Yii::$app->user->getIdentity()->consumer->id],
                                                    'visible' => Yii::$app->user->can('viewMyProfile'),
                                                ],
                                            ],
                                        ]) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php if (Yii::$app->user->can('manageConsumers') || Yii::$app->user->can('manageBusinesses') ||
                        Yii::$app->user->can('submitSales') || Yii::$app->user->can('managePlanes') || Yii::$app->user->can('manageConfigurations') || Yii::$app->user->can('manageUsers') || Yii::$app->user->can('manageQualifications')) : ?>
                            <div class="panel">
                                <div class="panel-heading" role="tab" id="headingFor">
                                    <a class="linkmenu" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseExample3" aria-expanded="true" aria-controls="collapseExample3">
                                        <h6 class="sidebar-title"><?= Yii::t('app', 'Entries') ?></h6>
                                    </a>
                                </div>
                                <div class="collapse" id="collapseExample3" role="tabpanel" aria-labelledby="headingFor">
                                    <?= Nav::widget([
                                            'options' => ['class' => 'nav-stacked nav-pills'],
                                            'items' => [
                                                [
                                                    'label' => Yii::t('app', 'Consumers'),
                                                    'url' => Yii::$app->user->can('admin') ? ['consumers/index'] : ['consumers/create'],
                                                    'visible' => Yii::$app->user->can('manageConsumers'),
                                                ],
                                                [
                                                    'label' => Yii::t('app', 'Businesses'),
                                                    'url' =>  (Yii::$app->user->can('salesReport') && !Yii::$app->user->can('admin') ? ['businesses/view', 'id' => Yii::$app->user->identity->authenticable_id] : ['businesses/index']),
                                                    'visible' => Consumer::find()->where(['id' => Yii::$app->user->identity->authenticable_id])->businessRepresentatives()->one() || Yii::$app->user->can('manageBusinesses') || Yii::$app->user->can('receptionist') ||  Yii::$app->user->can('salesReport'),
                                                ],
                                                [
                                                    'label' => Yii::t('app', 'Users'),
                                                    'url' => ['users/index'],
                                                    'visible' => Yii::$app->user->can('manageUsers') || Yii::$app->user->can('receptionist'),
                                                ],
                                                [
                                                    'label' => Yii::t('app', 'Configurations'),
                                                    'url' => ['configurations/index'],
                                                    'visible' => Yii::$app->user->can('manageConfigurations'),
                                                ],
                                                [
                                                    'label' => Yii::t('app', 'Qualifications'),
                                                    'url' => ['qualifications/index'],
                                                    'visible' => Yii::$app->user->can('manageQualifications'),
                                                ],
                                                [
                                                    'label' => Yii::t('app', 'Planes'),
                                                    'url' => ['planes/index'],
                                                    'visible' => Yii::$app->user->can('managePlanes'),
                                                ],
                                                [
                                                    'label' => Yii::t('app', 'Sales'),
                                                    'url' => ['sales/create'],
                                                    'visible' => Yii::$app->user->can('submitSales') || Yii::$app->user->can('receptionist'),
                                                ],
                                            ],
                                        ]) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if (Yii::$app->user->can('salesReport') || Yii::$app->user->can('transactionReport') || Yii::$app->user->can('receptionist')) : ?>
                                <div class="panel">
                                    <div class="panel-heading" role="tab" id="headingFive">
                                        <a class="linkmenu" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseExample4" aria-expanded="true" aria-controls="collapseExample4">
                                            <h6 class="sidebar-title"><?= Yii::t('app', 'Create Offer') ?></h6>
                                        </a>
                                    </div>
                                    <div class="collapse" id="collapseExample4" role="tabpanel" aria-labelledby="headingFive">
                                        <?= Nav::widget([
                                            'options' => ['class' => 'nav-stacked nav-pills'],
                                            'items' => [
                                                [
                                                    'label' => Yii::t('app', 'Ofertas'),
                                                    'url' => ['offer/index'],
                                                    'visible' => Yii::$app->user->can('salesReport') || Yii::$app->user->can('receptionist') || Yii::$app->user->can('transactionReport'),
                                                ]
                                            ],
                                        ]) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if (Yii::$app->user->can('salesReport') || Yii::$app->user->can('transactionReport') || Yii::$app->user->can('receptionist')) : ?>
                                <div class="panel">
                                    <div class="panel-heading" role="tab" id="headingOne">
                                        <a class="linkmenu" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseExample1" aria-expanded="true" aria-controls="collapseExample1">
                                            <h6 class="sidebar-title"><?= Yii::t('app', 'Reports') ?></h6>
                                        </a>
                                    </div>
                                    <div class="collapse" id="collapseExample1" role="tabpanel" aria-labelledby="headingOne">
                                        <?= Nav::widget([
                                            'options' => ['class' => 'nav-stacked nav-pills'],
                                            'items' => [
                                                [
                                                    'label' => Yii::t('app', 'Businesses'),
                                                    'url' => ['businesses/report'],
                                                    'visible' => Yii::$app->user->can('viewBusinessesReport'),
                                                ],
                                                [
                                                    'label' => Yii::t('app', 'Extract Sales'),
                                                    'url' => ['sales/report'],
                                                    'visible' => Yii::$app->user->can('salesReport'),
                                                ],
                                                [
                                                    'label' => Yii::t('app', 'Extract'),
                                                    'url' => ['consumers/report'],
                                                    'visible' => Yii::$app->user->can('transactionReport'),
                                                ],
                                                [
                                                    'label' => Yii::t('app', 'Region Consumption'),
                                                    'url' => ['consumers/representative-report'],
                                                    'visible' => Consumer::find()->where(['id' => Yii::$app->user->identity->authenticable_id])->businessRepresentatives()->one() || Yii::$app->user->can('receptionist') || Yii::$app->user->can('admin'),
                                                ],
                                                [
                                                    'label' => Yii::t('app', 'Comission'),
                                                    'url' => ['consumers/representative-comission'],
                                                    'visible' => Consumer::find()->where(['id' => Yii::$app->user->identity->authenticable_id])->businessRepresentatives()->one() || Yii::$app->user->can('admin'),
                                                ],
                                                [
                                                    'label' => Yii::t('app', 'Network Report'),
                                                    'url' => ['consumers/network-report'],
                                                    'visible' => Yii::$app->user->can('viewNetworkReport'),
                                                ],
                                            ],
                                        ]) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if (Yii::$app->user->can('admin') || Yii::$app->user->can('transactionReport')) : ?>
                                <div class="panel">
                                    <div class="panel-heading" role="tab" id="headingTwo">
                                        <a class="linkmenu" role="button" data-parent="#accordion" data-toggle="collapse" href="#collapseExample" aria-expanded="true" aria-controls="collapseExample">
                                            <h6 class="sidebar-title"><?= Yii::t('app', 'Investimento') ?></h6>
                                        </a>
                                    </div>
                                    <div class="collapse" id="collapseExample" role="tabpanel" aria-labelledby="headingTwo">
                                        <? 
                                        Nav::widget([
                                            'options' => ['class' => 'nav-stacked nav-pills'],
                                            'items' => [
                                                [
                                                    'label' => ((Investimento::validarContratoInvestimento(Yii::$app->user->identity->authenticable_id) && !Yii::$app->user->can('admin')) ? Yii::t('app', 'Visualizar Contrato') : Yii::t('app', 'Gerar Contrato')),
                                                    'url' => ((Investimento::validarContratoInvestimento(Yii::$app->user->identity->authenticable_id) && !Yii::$app->user->can('admin'))? ['investimento/visualizar-contrato'] : ['investimento/contrato-investimento']),
                                                    'linkOptions' => ((Investimento::validarContratoInvestimento(Yii::$app->user->identity->authenticable_id) && !Yii::$app->user->can('admin'))? ['target'=>'_blank'] : ['target'=>'']),
                                                    'visible' => Yii::$app->user->can('transactionReport'),
                                                ],
                                                [
                                                    'label' => Yii::t('app', 'Extrato'),
                                                    'url' => ['investimento/extract'],
                                                    'visible' => Yii::$app->user->can('transactionReport'),
                                                ],
                                                [
                                                    'label' => Yii::t('app', 'Porcentagem'),
                                                    'url' => ['investimento/porcentagem'],
                                                    'visible' => Yii::$app->user->can('admin'),
                                                ],
                                                [
                                                    'label' => Yii::t('app', 'Plane Investiment'),
                                                    'url' => ['plano-investimento/index'],
                                                    'visible' => Yii::$app->user->can('admin'),
                                                ]
                                            ],
                                        ]) 
                                        ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if (Yii::$app->user->can('admin')) : ?>
                                <div class="panel">
                                    <div class="panel-heading" role="tab" id="headingTree">
                                        <a class="linkmenu" role="button" data-parent="#accordion" data-toggle="collapse" href="#collapseExample2" aria-expanded="true" aria-controls="collapseExample2">
                                            <h6 class="sidebar-title"><?= Yii::t('app', 'Extract') ?></h6>
                                        </a>
                                    </div>
                                    <div class="collapse" id="collapseExample2" role="tabpanel" aria-labelledby="headingTree">
                                        <?= Nav::widget([
                                            'options' => ['class' => 'nav-stacked nav-pills'],
                                            'items' => [
                                                [
                                                    'label' => Yii::t('app', 'extract consumers'),
                                                    'url' => ['consumers/extract'],
                                                    'visible' => Yii::$app->user->can('admin'),
                                                ]
                                            ],
                                        ]) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ( Yii::$app->user->can('salesReport') || Yii::$app->user->can('admin')) : ?>
                                <div class="panel">
                                    <div class="panel-heading" role="tab" id="headingFife">
                                        <a class="linkmenu" role="button" data-parent="#accordion" data-toggle="collapse" href="#collapseExample5" aria-expanded="true" aria-controls="collapseExample5">
                                            <h6 class="sidebar-title"><?= Yii::t('app', 'Contratos') ?></h6>
                                        </a>
                                    </div>
                                    <div class="collapse" id="collapseExample5" role="tabpanel" aria-labelledby="headingFife">
                                        <?= Nav::widget([
                                            'options' => ['class' => 'nav-stacked nav-pills'],
                                            'items' => [
                                                [
                                                    'label' => Yii::t('app', 'Contratos'),
                                                    'url' => ['contratos/index'],
                                                    'visible' => Yii::$app->user->can('admin'),
                                                ],
                                                [
                                                    'label' => Yii::t('app', 'view contract'),
                                                    'url' => ['contratos/view'],
                                                    'visible' => !Yii::$app->user->can('admin'),
                                                ]
                                            ],
                                        ]) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if((Yii::$app->user->can('manageConsumers') && isset(Yii::$app->user->identity->consumer->identifier) && !Yii::$app->user->can('submitSales')) || Yii::$app->user->can('admin')){ ?>
                                <a href="https://loja.jedax.com.br/index.php?route=account/acessotbest&username=<?= Yii::$app->user->identity->consumer->identifier; ?>&token=<?php echo password_hash(Yii::$app->user->identity->consumer->identifier."tbestsistema", PASSWORD_BCRYPT); ?>" class="btn btn-success" target="_blank" style="margin-bottom: 20px; margin-top: 10px;"> Acessa Loja Virtual </a>
                            <?php } ?>
                        </div>
                        <h6 class="sidebar-title visible-xs-block"><?= Yii::t('app', 'User') ?></h6>
                        <?= Nav::widget([
                            'options' => ['class' => 'nav-stacked nav-pills visible-xs-block visible-sm-block'],
                            'items' => [
                                ['label' => Yii::t('app', 'Change Password'), 'url' => ['site/change-password']],
                                Yii::$app->user->isGuest ? (
                                    ['label' => 'Login', 'url' => ['site/login']]
                                ) : ['label' => 'Logout', 'url' => ['site/logout']],
                            ],
                        ]) ?>
                    </div>
                </nav>
            </div>
        <?php } ?>
        <div class="col-md-<?= Yii::$app->user->isGuest ? '12' : '9' ?> remove-padding-left">
            <nav class="navbar navbar-default remove-margin-bottom hidden-xs hidden-sm">
                <div class="container-fluid">
                    <div class="row">
                        <div class="navbar-left datanav">
                            <i class="fa fa-calendar" aria-hidden="true"></i>&nbsp;&nbsp;<?= date('m/Y'); ?>
                        </div>
                        <?php if(isset(Yii::$app->user->getIdentity()->name)){ ?>
                            <ul class="nav navbar-nav navbar-right">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-user" aria-hidden="true"></i>&nbsp;&nbsp;<strong><?= \Yii::$app->user->getIdentity()->name; ?> (<?= Yii::t('app', ucfirst(implode(', ', $roles))); ?>)</strong> 
                                        <span class="caret"></span>
                                    </a>
                                    <?= Nav::widget([
                                        'options' => ['class' => 'dropdown-menu'],
                                        'items' => [
                                            ['label' => Yii::t('app', 'Change Password'), 'url' => ['site/change-password']],
                                            Yii::$app->user->isGuest ? (
                                                ['label' => 'Login', 'url' => ['site/login']]
                                            ) : ['label' => 'Logout', 'url' => ['site/logout']],
                                        ],
                                    ]) ?>
                                </li>
                            </ul>
                        <?php } ?>
                    </div>
                </div>
            </nav>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= Alert::widget() ?>
            <div class="container-fluid margintop18">
                <?php if(isset(Yii::$app->user->identity->consumer->identifier)){ ?>
                    <div class="alert alert-success text-center" id="link-cadastro-fora" style="cursor: copy;" title="Clique para copiar" role="alert">
                        <input type="text" value="https://sistema.jedax.com.br/consumers/cadastro-fora?identificador=<?php echo Yii::$app->user->identity->consumer->identifier; ?>" style="top: -1900px; position: relative;" name="linkcopiar" id="linkcopiar" />
                        <buttton class="alert-link" style="margin-right: 100px;" data-toggle="tooltip" data-placement="top" >Convide um Consumidor - https://sistema.jedax.com.br/consumers/cadastro-fora?identificador=<?php echo Yii::$app->user->identity->consumer->identifier; ?> <i class="fa fa-clipboard"></i></button>
                    </div>
                <?php } ?>
                <?php if(Yii::$app->user->can('submitSales')){ ?>
                    <div class="alert alert-success text-center" id="link-cadastro-fora" style="cursor: copy;" title="Clique para copiar" role="alert">
                        <a href="<?= Url::toRoute(['businesses/view', 'id' => Yii::$app->user->identity->authenticable_id, 'tab'=>'image']); ?>" class="alert-link" style="margin-right: 100px;" data-toggle="tooltip" data-placement="top" >Convido ao convenio, adicionar uma logo no seu cadastro, clicando aqui <i class="fa fa-upload"></i></a>
                    </div>
                <?php } ?>
                <?= $content ?>
            </div>
        </div>
    </div>
</div>
<footer class="footer footernovo">
    <div class="container outer-container">
        <p class="pull-left">&copy; Jedax <?= date('Y') ?> | <a href="/site/terms" target="_blank"><?= Yii::t('app', 'Terms and Conditions') ?></a></p>
        <p class="pull-right" style="width: 120px;" ><a href="https://play.google.com/store/apps/details?id=com.tbest.tbestconsumo"><img src="<?= Url::base() ?>/images/disponivelplaystore.png" class="img-responsive" alt="Aplicativo Tbest" ></a></p>
    </div>
</footer>
<?php $this->endBody() ?>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<script>
    $("#link-cadastro-fora").on('click', function(){
        var textoCopiado = document.getElementById("linkcopiar");
        textoCopiado.select();
        document.execCommand("Copy");
        alert("Texto Copiado");
    })
</script>
</body>
</html>
<?php $this->endPage() ?>
