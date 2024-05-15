<?php
use app\models\PorcentagemInvestimento;
use app\models\InvestimentoDetail;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

$this->title = Yii::t('app', 'Relatório Antes de Gravar');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Porcentagem'), 'url' => ['porcentagem']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="configuration-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= Html::a(Yii::t('app', 'Confirmar'), ['gerar-juros', 'id' => $codigo], ['class' => 'btn btn-primary']) ?>
    <p></p>
    <table class="table table-bordered">
        <thead>
            <th>Codigo</th>
            <th>Nome</th>
            <th>Total</th>
            <th>Juros</th>
            <th>Saldo</th>
        </thead>
        <tbody>
            <?php foreach($dados as $consumidor){ ?>
                <tr>
                    <td><?= $consumidor['codigo']; ?></td>
                    <td><?= $consumidor['name']; ?></td>
                    <td><?= \Yii::$app->formatter->asCurrency($consumidor['total']); ?></td>
                    <td><?= \Yii::$app->formatter->asCurrency($consumidor['juros']); ?></td>
                    <td><?= \Yii::$app->formatter->asCurrency($consumidor['saldo']); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>