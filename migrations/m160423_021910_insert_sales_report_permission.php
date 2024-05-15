<?php

use yii\db\Migration;

class m160423_021910_insert_sales_report_permission extends Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->createPermission('salesReport');
        $permission->description = 'Sales Report';
        $auth->add($permission);
        $auth->addChild($auth->getRole('admin'), $permission);
    }

    public function down()
    {
        $auth->removeItem($auth->getPermission('salesReport'));
    }
}
