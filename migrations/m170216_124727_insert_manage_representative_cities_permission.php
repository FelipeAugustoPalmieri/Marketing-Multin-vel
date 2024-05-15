<?php

use yii\db\Migration;

class m170216_124727_insert_manage_representative_cities_permission extends Migration
{
     public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->createPermission('manageRepresentativeCities');
        $permission->description = 'Manage Representative Cities';
        $auth->add($permission);
        $auth->addChild($auth->getRole('admin'), $permission);
    }

    public function safeDown()
    {
        $auth->removeItem($auth->getPermission('manageRepresentativeCities'));
    }
}
