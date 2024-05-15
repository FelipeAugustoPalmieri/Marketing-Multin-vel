<?php

use yii\db\Migration;

class m170217_113442_create_sales_representative_cities extends Migration
{
    public function up()
    {
        $this->createTable('sales_representative_cities', [
            'id' => $this->primaryKey(),
            'sales_representative_id' => $this->integer()->notNull(),
            'city_id' => $this->integer()->notNull()->unique(),
        ]);

        $this->addForeignKey(
            'fk-sales_representative_cities-sales_representative_id',
            'sales_representative_cities',
            'sales_representative_id',
            'consumers',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-sales_representative_cities-city_id',
            'sales_representative_cities',
            'city_id',
            'cities',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('sales_representative_cities');
    }
}
