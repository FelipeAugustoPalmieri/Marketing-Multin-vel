<?php

use yii\db\Migration;

class m160412_002039_create_users extends Migration
{
    public function safeUp()
    {
        $this->createTable(
            'users',
            [
                'id' => $this->primaryKey(),
                'created_at' => $this->timestamp()->notNull(),
                'updated_at' => $this->timestamp(),
                'login' => $this->string()->notNull()->unique(),
                'email' => $this->string()->notNull()->unique(),
                'encrypted_password' => $this->string()->notNull(),
            ]
        );
    }

    public function safeDown()
    {
        $this->dropTable('users');
    }
}
