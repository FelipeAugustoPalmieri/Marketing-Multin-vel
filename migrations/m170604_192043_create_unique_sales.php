<?php

use yii\db\Migration;

class m170604_192043_create_unique_sales extends Migration
{
    public function up()
    {
         $this->createIndex('uk_business_invoice', 'sales', ['business_id', 'invoice_code'], $unique = true);
    }

    public function down()
    {
        echo "m170604_192043_create_unique_sales cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
