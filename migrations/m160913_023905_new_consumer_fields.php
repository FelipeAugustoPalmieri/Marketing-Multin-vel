<?php

use yii\db\Migration;

class m160913_023905_new_consumer_fields extends Migration
{
    public function safeUp()
    {
        $this->addColumn('physical_persons', 'issuing_body', $this->string());

        $this->addColumn('legal_persons', 'address_complement', $this->string());

    }

    public function safeDown()
    {
        $this->dropColumn('physical_persons', 'issuing_body');

        $this->dropColumn('legal_persons', 'address_complement');
    }
}
