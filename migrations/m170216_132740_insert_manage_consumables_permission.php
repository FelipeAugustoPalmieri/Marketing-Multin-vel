<?php

use yii\db\Migration;

class m170216_132740_insert_manage_consumables_permission extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->createPermission('manageConsumables');
        $permission->description = 'Manage Consumables';
        $auth->add($permission);
        $auth->addChild($auth->getRole('admin'), $permission);
    }

    public function safeDown()
    {
        $auth->removeItem($auth->getPermission('manageConsumables'));
    }
}
