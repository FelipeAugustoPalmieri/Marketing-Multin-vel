<?php

use yii\db\Migration;

class m170308_185823_insert_permission_admin_manage_consumers extends Migration
{
     public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->createPermission('adminManageConsumers');
        $permission->description = 'Admin Manage Consumers';
        $auth->add($permission);
        $auth->addChild($auth->getRole('admin'), $permission);
    }

    public function safeDown()
    {
        $auth->removeItem($auth->getPermission('adminManageConsumers'));
    }
}

