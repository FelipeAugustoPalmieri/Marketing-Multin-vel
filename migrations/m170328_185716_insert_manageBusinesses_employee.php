<?php

use yii\db\Migration;

class m170328_185716_insert_manageBusinesses_employee extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->getPermission('manageBusinesses');
        $auth->addChild($auth->getRole('employee'), $permission);
    }

    public function safeDown()
    {
        $auth->removeItem($auth->getPermission('manageBusinesses'));
    }
}
