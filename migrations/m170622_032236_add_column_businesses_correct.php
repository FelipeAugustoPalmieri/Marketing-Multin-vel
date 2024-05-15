<?php

use yii\db\Migration;

class m170622_032236_add_column_businesses_correct extends Migration
{
    public function up()
    {
        $this->dropColumn('businesses', 'is_disable');
        $this->addColumn('businesses', 'is_disabled', $this->boolean()->notNull()->defaultValue(false));
    }

    public function down()
    {
        $this->addColumn('businesses', 'is_disable', $this->boolean()->notNull()->defaultValue(false));
        $this->dropColumn('businesses', 'is_disabled');
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
