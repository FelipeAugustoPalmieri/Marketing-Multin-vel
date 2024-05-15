<?php

use yii\db\Migration;

/**
 * Handles the creation for table `sales`.
 */
class m160601_173615_create_sales extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('sales', [
            'id' => $this->primaryKey(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp(),
            'sold_at' => $this->timestamp()->notNull(),
            'total' => $this->float()->notNull(),
            'invoice_code' => $this->string()->notNull(),
            'consumer_id' => $this->integer()->notNull(),
            'business_id' => $this->integer()->notNull(),
            'consumable_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-sales-consumer_id',
            'sales',
            'consumer_id',
            'consumers',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-sales-business_id',
            'sales',
            'business_id',
            'businesses',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-sales-consumable_id',
            'sales',
            'consumable_id',
            'consumables',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('sales');
    }
}

