<?php

use yii\db\Migration;

class m170330_132156_insert_manageBusinessUsers_admin extends Migration
{
     public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->createPermission('manageBusinessUsers');
        $permission->description = 'Manage Business users';
        $auth->add($permission);
        $auth->addChild($auth->getRole('admin'), $permission);
    }

    public function safeDown()
    {
        $auth->removeItem($auth->getPermission('manageBusinessUsers'));
    }
}
