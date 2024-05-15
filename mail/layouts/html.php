<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <h1 style="display:block;font-family:Arial;font-size:34px;font-weight:bold;line-height:120%;margin-top:20px;margin-right:0;margin-bottom:20px;margin-left:0;text-align:left;padding-top:10px;color:#f58634!important">
        <?= Html::encode($this->title) ?>
    </h1>
    <div style="color:#505050;font-family:Arial;font-size:16px;line-height:164%;text-align:left;max-width:100%">
        <?= $content ?>
    </div>
    <div style="border-top: 1px solid #505050; margin: 1em 0; padding: .5em 0; text-align: center;">
        <a href="<?= Url::base(true) ?>/"><img width="140px" src="<?= Url::base(true) ?>/images/newlogotipo-tbest-login.png" alt="TBest" /></a>
    </div>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
