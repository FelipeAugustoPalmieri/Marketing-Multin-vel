<?php

use yii\db\Migration;

class m161005_205024_create_transaction_details extends Migration
{
    public function up()
    {
        $this->createTable('transaction_details', [
            'id' => $this->primaryKey(),
            'object_type' => $this->string()->notNull(),
            'object_id' => $this->integer()->notNull(),
            'consumer_id' => $this->integer()->notNull(),
            'plane_id' => $this->integer()->notNull(),
            'profit_percentage' => $this->float()->notNull(),
            'profit' => $this->float()->notNull(),
            'transaction_origin' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-sales-consumer_id',
            'transaction_details',
            'consumer_id',
            'consumers',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-sales-planes_id',
            'transaction_details',
            'plane_id',
            'planes',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
    }

    public function down()
    {
        $this->dropTable('transaction_details');
    }
}
