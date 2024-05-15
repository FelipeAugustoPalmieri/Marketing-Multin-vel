<?php
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = Yii::t('app/mail', 'Proof Of Purchase');
$url = Url::to(['/site/login'], true);
$data = explode(" ", $sale->created_at);
$aqui = Html::a(Yii::t('app/mail', 'here'), Url::to(['/site/login'], true))


?>
<p style="text-align:center"><?= Yii::t('app/mail', 'Hello {name}, your purchase at', ['name' => $sale->consumer->legalPerson->name]) ?></p>
<p style="text-align:center"><strong><?= $sale->business->legalPerson->name; ?></strong>
<?= Yii::t('app/mail', ', on {date] {hour}', ['date' => \Yii::$app->formatter->asDate($data[0]), 'hour' =>$data[1]]) ?></p>
<p style="text-align:center"><?= Yii::t('app/mail', 'in the amount of {value}', ['value' => \Yii::$app->formatter->asCurrency($sale->total)]) ?>
<?= Yii::t('app/mail',  ' under Invoice: {invoice}, generated', ['invoice' => $sale->invoice_code]) ?>
<span style="font-size:24px;color:#f58634!important;" ><strong><?= Yii::t('app/mail', '{value_consumer} points.', ['value_consumer' => \Yii::$app->formatter->asDecimal($sale->calculatePoints())]) ?></strong></span></p>
<p style="text-align:center"><?= Yii::t('app/mail', 'Click {here} to access your account', ['here' => $aqui]) ?></p>
