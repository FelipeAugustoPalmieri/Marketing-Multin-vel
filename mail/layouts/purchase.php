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

    <table width="100%">
        <tr>
            <td width="10%" style="text-align: center; padding: 0;">
                <div style="margin: 4px 0; padding: .5em 0; text-align: center;">
                <a href="<?= Url::base(true) ?>/"><img alt="TBest" src="<?= Url::base(true) ?>/images/newlogotipo-tbest-login.png" width="90px" style="margin: 0; border: 0; padding: 0; display: block;"></a>
                </div>
            </td>
            <td width="90%" style="text-align: left;padding: 0;">
                <p style= "margin:4px 0; font-size:14px">TBEST Consumo Inteligente LTDA</p>
                <p style= "margin:4px 0; font-size:14px">33.394.220/0001-04</p>
                <p style= "margin:4px 0;font-size:14px">Av. Fernando Machado, 3290D, Bairro Lider, CEP 89805-203</p>
                <p style= "margin:4px 0;font-size:14px">Chapecó - SC</p>
            </td>
        </tr>
    </table>
    <hr>
    <?php $this->beginBody() ?>
    <h1 style="display:block;font-family:Arial;font-size:20px;font-weight:bold;line-height:120%;margin-top:10px;margin-right:0;margin-bottom:10px;margin-left:0;text-align:center;padding-top:10px;color:#f58634!important; text-transform:uppercase;">
        <?= Html::encode($this->title) ?>
    </h1>
    <div style="color:#000000;font-family:Arial;font-size:16px;line-height:164%;text-align:center;max-width:100%">
        <?= $content ?>
    </div>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
