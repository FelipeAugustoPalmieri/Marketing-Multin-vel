<?php

use yii\db\Migration;

class m160426_190733_create_configurations extends Migration
{
    public function up()
    {
        $this->createTable(
           'configurations',
           [
               'id' => $this->primaryKey(),
               'name' => $this->string()->notNull()->unique(),
               'type' => $this->string()->notNull(),
               'value' => $this->string()->notNull(),
           ]
       );
    }

    public function down()
    {
        $this->dropTable('configurations');
    }
}
