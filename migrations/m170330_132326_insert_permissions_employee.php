<?php

use yii\db\Migration;

class m170330_132326_insert_permissions_employee extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->getPermission('manageBusinessUsers');
        $auth->addChild($auth->getRole('employee'), $permission);

        $permission = $auth->getPermission('manageConsumables');
        $auth->addChild($auth->getRole('employee'), $permission);
    }

    public function safeDown()
    {
        $auth->removeItem($auth->getPermission('manageBusinessUsers'));
        $auth->removeItem($auth->getPermission('manageConsumables'));
    }
}
