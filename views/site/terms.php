<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Terms and Conditions');
$url = Url::to(['/'], true);

?>
<h1><?= Html::encode($this->title) ?></h1>

<iframe src="<?= $url ?>terms/regulamento-tbest.pdf?embedded=true" style="width:100%; height:550px;" frameborder="0"><?= $url ?></iframe>
