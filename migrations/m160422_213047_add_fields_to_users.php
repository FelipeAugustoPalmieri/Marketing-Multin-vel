<?php

use yii\db\Migration;

class m160422_213047_add_fields_to_users extends Migration
{
    public function safeUp()
    {
        $this->addColumn('users', 'name', $this->string()->notNull());
        $this->addColumn('users', 'authenticable_type', $this->string());
        $this->addColumn('users', 'authenticable_id', $this->integer());

        $this->createIndex('idx_users_authenticable', 'users', ['authenticable_type', 'authenticable_id']);
        $this->createIndex('idx_users_login', 'users', ['login']);
    }

    public function safeDown()
    {
        $this->dropIndex('idx_users_authenticable', 'users');
        $this->dropIndex('idx_users_login', 'users');

        $this->dropColumn('users', 'name');
        $this->dropColumn('users', 'authenticable_type');
        $this->dropColumn('users', 'authenticable_id');
    }
}
