<?php

use yii\db\Migration;

class m170619_203650_insert_viewNetworkReport_consumer extends Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->createPermission('viewNetworkReport');
        $permission->description = 'View Network Report';
        $auth->add($permission);
        $auth->addChild($auth->getRole('consumer'), $permission);
    }

    public function down()
    {
        $auth->removeItem($auth->getPermission('viewNetworkReport'));
    }
}
