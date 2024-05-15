<?php

use yii\db\Migration;

class m170612_180857_add_columns_physical_persons extends Migration
{
    public function safeUp()
    {
        $this->addColumn('physical_persons', 'pis', $this->string()->unique());
        $this->addColumn('physical_persons', 'partner_issuing_body', $this->string());
    }

    public function safeDown()
    {
        $this->dropColumn('physical_persons', 'pis');
        $this->dropColumn('physical_persons', 'partner_issuing_body');

    }
}