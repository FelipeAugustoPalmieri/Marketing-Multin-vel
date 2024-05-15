<?php

use yii\db\Migration;

class m160818_204021_insert_enable_consumer extends Migration
{
     public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->createPermission('enableConsumer');
        $permission->description = 'Enable Consumers';
        $auth->add($permission);
        $auth->addChild($auth->getRole('admin'), $permission);
    }

    public function safeDown()
    {
        $auth->removeItem($auth->getPermission('enableConsumer'));
    }
}
