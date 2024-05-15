<?php

use yii\db\Migration;

class m160921_201443_insert_view_my_profile extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->createPermission('viewMyProfile');
        $permission->description = 'View Consumer Profile';
        $auth->add($permission);
        $auth->addChild($auth->getRole('consumer'), $permission);
    }

    public function safeDown()
    {
        $auth->removeItem($auth->getPermission('viewMyProfile'));
    }
}

