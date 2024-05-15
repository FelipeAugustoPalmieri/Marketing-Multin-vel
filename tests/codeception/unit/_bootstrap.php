<?php
use perspectiva\phactory\DbCleaner;
new yii\web\Application(require(dirname(__DIR__) . '/config/unit.php'));
DbCleaner::recreate();
