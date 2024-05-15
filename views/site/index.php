<?php
use app\helpers\DashboardHelper;
use yii\helpers\Url;
use yii\web\View;

$this->title = 'TBest - ' . \Yii::$app->user->getIdentity()->name;

$roles = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->id));
?>
<?php
    yii\bootstrap\Modal::begin(['id' =>'modal-network-tree']);
    yii\bootstrap\Modal::end();
?>
<?php
    yii\bootstrap\Modal::begin([
        'id' =>'modal',
        'size' => 'modal-gigante',
        'header' => '<h2>' . \Yii::$app->user->getIdentity()->name . '</h2>',
    ]);
    yii\bootstrap\Modal::end();
?>

    <div class="row">
        <div class="col-sm-4">
            <div class="alert-padding-menor">
                <?= DashboardHelper::getMonthPointsBar(); ?>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="alert-padding-menor">
                <?= DashboardHelper::getQualificationBar(); ?>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card text-xs-center">
                <div class="card-header">
                    <strong><?= Yii::t('app', 'Network Points'); ?></strong>
                </div>
                <div class="card-block" style="padding: 1.1em !important;">
                    <h4 class="card-title" style="margin: 0 !important;">
                    <?php
                        $consumer = \Yii::$app->user->getIdentity()->consumer ?? null;
                        echo $consumerMonthPoints = $consumer ? $consumer->getMonthPoints(date('m'), date('Y')) : ""; ?>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <hr />

    <div class="row">
        <div class="col-sm-12">
            <h2>Rede</h2>
            <div class="chart" id="network-tree" style="width:100%; height: 400px"></div>
        </div>
    </div>

    <?php
    $consumer = \Yii::$app->user->getIdentity()->consumer ?? null;
    $tree = $consumer ? $consumer->getTree() : null;
    if($tree) {
        $this->registerJs("
            var treeData = " . json_encode(DashboardHelper::getArrayTree($tree)) . ";
        ", View::POS_HEAD, 'tree-data');
    }
    $this->registerCssFile(Url::base() . '/js/treant-js-master/Treant.css', ['depends' => [\yii\web\JqueryAsset::className()]]);
    $this->registerCssFile(Url::base() . '/css/tree.css', ['depends' => [\yii\web\JqueryAsset::className()]]);
    $this->registerJsFile(Url::base() . '/js/treant-js-master/vendor/raphael.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
    $this->registerJsFile(Url::base() . '/js/treant-js-master/Treant.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
    $this->registerJsFile(Url::base() . '/js/treant-js-master/vendor/jquery.easing.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
    $this->registerJsFile(Url::base() . '/js/tree.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
    $this->registerJsFile(Url::base() . '/js/tree-modal.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
    ?>



<?php $this->registerJs(
    "$(function() {
        $('[data-modal=\"modal\"]').click(function(e) {
            e.preventDefault();
            $('#modal').modal('show').find('.modal-body')
            .load($(this).attr('href'));
        });
    });"
); ?>