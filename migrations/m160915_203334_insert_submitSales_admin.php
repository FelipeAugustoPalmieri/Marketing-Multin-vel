<?php

use yii\db\Migration;

class m160915_203334_insert_submitSales_admin extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->getPermission('submitSales');
        $auth->addChild($auth->getRole('admin'), $permission);
    }

    public function safeDown()
    {
        $auth->removeItem($auth->getPermission('submitSales'));
    }
}
