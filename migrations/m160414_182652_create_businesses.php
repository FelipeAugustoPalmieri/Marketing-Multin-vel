<?php

use yii\db\Migration;

class m160414_182652_create_businesses extends Migration
{
    public function safeUp()
    {
        $this->createTable(
            'businesses',
            [
                'id' => $this->primaryKey(),
                'created_at' => $this->timestamp()->notNull(),
                'updated_at' => $this->timestamp(),
                'legal_person_type' => $this->string()->notNull(),
                'legal_person_id' => $this->integer()->notNull(),
            ]
        );
        $this->createIndex('uk_businesses', 'businesses', ['legal_person_type', 'legal_person_id'], true);
    }

    public function down()
    {
        $this->dropTable('businesses');
    }
}
