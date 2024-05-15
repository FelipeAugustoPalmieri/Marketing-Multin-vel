<?php

use yii\db\Migration;

class m170619_132842_add_column_businesses extends Migration
{
    public function up()
    {
        $this->addColumn('businesses', 'is_disable', $this->boolean()->notNull()->defaultValue(false));
    }

    public function down()
    {
        $this->dropColumn('businesses', 'is_disable');
    }
}
