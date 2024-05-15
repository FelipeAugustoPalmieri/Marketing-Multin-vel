/*<?php

use yii\db\Migration;

class m160516_175946_create_consumables extends Migration
{
    public function up()
    {
        $this->createTable('consumables', [
            'id' => $this->primaryKey(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp(),
            'business_id' => $this->integer()->notNull(),
            'description' => $this->string()->notNull()->unique(),
            'shared_percentage' => $this->float(),
        ]);

        $this->addForeignKey(
            'fk-consumables-business_id',
            'consumables',
            'business_id',
            'businesses',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('consumables');
    }
}
