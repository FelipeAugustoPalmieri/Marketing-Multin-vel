<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app/mail', 'Reset TBest Password');
?>
<p><?= Yii::t('app/mail', '{name}, click on the link bellow to reset your password:', ['name' => $name]) ?></p>
<p><?= Html::a(
    Url::to(['/site/reset-password', 'token' => $token], true),
    Url::to(['/site/reset-password', 'token' => $token], true)
) ?></p>
<p>
    <?= Yii::t('app/mail', 'This link only will be valid only for once.') ?>
    <?= Yii::t('app/mail', 'If you didn\'t request to reset your password, please ignore this message.') ?>
</p>
