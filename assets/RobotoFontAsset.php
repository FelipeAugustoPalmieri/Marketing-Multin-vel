<?php
namespace app\assets;

use yii\web\AssetBundle;

class RobotoFontAsset extends AssetBundle
{
    public $sourcePath = '@bower/roboto-fontface/';
    public $css = [
        'css/roboto-fontface.css',
    ];
}
