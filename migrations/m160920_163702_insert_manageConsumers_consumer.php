<?php

use yii\db\Migration;

class m160920_163702_insert_manageConsumers_consumer extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->getPermission('manageConsumers');
        $auth->addChild($auth->getRole('consumer'), $permission);
    }

    public function safeDown()
    {
        $auth->removeItem($auth->getPermission('manageConsumers'));
    }
}

