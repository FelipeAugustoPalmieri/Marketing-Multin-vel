<?php

use yii\db\Migration;

class m161010_200639_insert_transaction_report extends Migration
{
     public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->createPermission('transactionReport');
        $permission->description = 'Transcation Reports';
        $auth->add($permission);
        $auth->addChild($auth->getRole('admin'), $permission);

        $permission = $auth->getPermission('transactionReport');
        $auth->addChild($auth->getRole('consumer'), $permission);
    }

    public function safeDown()
    {
        $auth->removeItem($auth->getPermission('transactionReport'));
    }
}
