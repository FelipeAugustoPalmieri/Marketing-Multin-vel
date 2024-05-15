<?php

use yii\db\Migration;

class m161006_181714_add_columns_created_updated_transaction_details extends Migration
{
    public function up()
    {
        $this->addColumn('transaction_details', 'created_at', $this->timestamp()->notNull());
        $this->addColumn('transaction_details', 'updated_at', $this->timestamp());
    }
    public function down()
    {
        $this->dropColumn('created_at');
        $this->dropColumn('updated_at');
    }
}
