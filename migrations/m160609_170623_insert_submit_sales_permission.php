<?php

use yii\db\Migration;

class m160609_170623_insert_submit_sales_permission extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->createPermission('submitSales');
        $permission->description = 'Submit Sales';
        $auth->add($permission);
        $auth->addChild($auth->getRole('business'), $permission);
    }

    public function safeDown()
    {
        $auth->removeItem($auth->getPermission('submitSales'));
    }
}
