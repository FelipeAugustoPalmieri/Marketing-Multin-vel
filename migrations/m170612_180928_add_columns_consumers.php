<?php

use yii\db\Migration;

class m170612_180928_add_columns_consumers extends Migration
{
    public function safeUp()
    {
        $this->addColumn('consumers', 'operation', $this->integer());
        $this->addColumn('consumers', 'bank_number', $this->string());
    }

    public function safeDown()
    {
        $this->dropColumn('consumers', 'operation');
        $this->dropColumn('consumers', 'bank_number');

    }
}