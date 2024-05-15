<?php

use yii\db\Migration;

class m170720_130621_insert_viewBusinessesReport_permission extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->createPermission('viewBusinessesReport');
        $permission->description = 'View Businesses Report';
        $auth->add($permission);
        $auth->addChild($auth->getRole(['admin']), $permission);
    }

    public function safeDown()
    {
        $auth->removeItem($auth->getPermission('viewBusinessesReport'));
    }
}