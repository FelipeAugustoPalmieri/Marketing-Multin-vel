<?php

use yii\db\Migration;

class m170720_130713_insert_viewBusinessesReport_other_roles extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->getPermission('viewBusinessesReport');
        $auth->addChild($auth->getRole('employee'), $permission);
        $auth->addChild($auth->getRole('consumer'), $permission);
        $auth->addChild($auth->getRole('representative'), $permission);
    }

    public function safeDown()
    {
        $auth->removeItem($auth->getPermission('viewBusinessesReport'));
    }
}