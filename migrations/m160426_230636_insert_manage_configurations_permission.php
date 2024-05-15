<?php

use yii\db\Migration;

class m160426_230636_insert_manage_configurations_permission extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->createPermission('manageConfigurations');
        $permission->description = 'Manage Configurations';
        $auth->add($permission);
        $auth->addChild($auth->getRole('admin'), $permission);
    }

    public function safeDown()
    {
        $auth->removeItem($auth->getPermission('manageConfigurations'));
    }
}
