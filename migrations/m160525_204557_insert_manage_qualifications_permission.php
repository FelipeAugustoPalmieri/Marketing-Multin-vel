<?php

use yii\db\Migration;

class m160525_204557_insert_manage_qualifications_permission extends Migration
{
     public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->createPermission('manageQualifications');
        $permission->description = 'Manage Qualifications';
        $auth->add($permission);
        $auth->addChild($auth->getRole('admin'), $permission);
    }

    public function safeDown()
    {
        $auth->removeItem($auth->getPermission('manageQualifications'));
    }
}
