<?php

use yii\db\Migration;

class m170613_172613_add_collumn_planes extends Migration
{
    public function safeUp()
    {
        $this->addColumn('planes', 'pay_plane', $this->string());
    }

    public function safeDown()
    {
        $this->dropColumn('planes', 'pay_plane');
    }
}
