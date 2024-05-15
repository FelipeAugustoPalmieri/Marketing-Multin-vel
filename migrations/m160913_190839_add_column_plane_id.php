<?php

use yii\db\Migration;

class m160913_190839_add_column_plane_id extends Migration
{
    public function up()
    {
        $this->addColumn(
            'consumers',
            'plane_id',
            'integer'
        );

        $this->addForeignKey(
            'fk-consumers-plane_id',
            'consumers',
            'plane_id',
            'planes',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
    }

    public function down()
    {
        $this->dropColumn('plane_id');
    }
}
