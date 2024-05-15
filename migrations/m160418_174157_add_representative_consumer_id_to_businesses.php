<?php

use yii\db\Migration;

class m160418_174157_add_representative_consumer_id_to_businesses extends Migration
{
    public function safeUp()
    {
        $this->addColumn('businesses', 'representative_consumer_id', $this->integer());
        $this->addForeignKey(
            'fk-businesses-representative_consumer_id',
            'businesses',
            'representative_consumer_id',
            'consumers',
            'id'
        );
    }

    public function down()
    {
        $this->dropColumn('businesses', 'representative_consumer_id');
    }
}
