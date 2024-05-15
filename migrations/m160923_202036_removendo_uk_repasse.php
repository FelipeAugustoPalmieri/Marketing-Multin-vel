<?php

use yii\db\Migration;

class m160923_202036_removendo_uk_repasse extends Migration
{
    public function up()
    {
        $this->execute("ALTER TABLE consumables DROP CONSTRAINT consumables_description_key");
    }

    public function down()
    {
        echo "m160923_202036_removendo_uk_repasse cannot be reverted.\n";

        return false;
    }
}
