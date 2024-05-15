<?php
use yii\helpers\Url;

$this->title = Yii::t('app/mail', 'Welcome to TBest System!');
$url = Url::to(['/site/login'], true);
?>
<p><?= Yii::t('app/mail', '{name}, now you can sign in on TBest System with the following credentials:', ['name' => $name]) ?></p>
<ul>
    <li><strong><?= Yii::t('app/mail', 'URL') ?>:</strong> <a href="<?= $url ?>"><?= $url ?></a></li>
    <li><strong><?= Yii::t('app/mail', 'Username') ?>:</strong> <?= $login ?></li>
    <li><strong><?= Yii::t('app/mail', 'Password') ?>:</strong> <?= $password ?></li>
</ul>

