<?php

use yii\db\Migration;

class m161004_202603_add_columns_planes extends Migration
{
    public function up()
    {
        $this->addColumn('planes', 'value', $this->float());
        $this->addColumn('planes', 'bonus_percentage', $this->float());
    }

    public function down()
    {
        $this->dropColumn('planes', 'value');
        $this->dropColumn('planes', 'bonus_percentage');
    }
}
