<?php

use yii\db\Migration;

class m160811_172805_insert_manage_planes_permission extends Migration
{
     public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->createPermission('managePlanes');
        $permission->description = 'Manage Planes';
        $auth->add($permission);
        $auth->addChild($auth->getRole('admin'), $permission);
    }

    public function safeDown()
    {
        $auth->removeItem($auth->getPermission('managePlanes'));
    }
}
