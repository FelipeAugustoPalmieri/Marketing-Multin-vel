<?php

use yii\db\Migration;

class m161015_181034_campos_novos_sale extends Migration
{
    public function safeUp()
    {
        $this->addColumn('sales', 'shared_percentage', $this->float());
        $this->addColumn('sales', 'plane_multiplier', $this->float());
        $this->addColumn('sales', 'plane_id', $this->integer());
        $this->addColumn('sales', 'unshared_fees', $this->float());

        $this->addForeignKey(
            'fk-sales-plane_id',
            'sales',
            'plane_id',
            'planes',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
    }

    public function safeDown()
    {
        $this->dropColumn('sales', 'shared_percentage');
        $this->dropColumn('sales', 'plane_multiplier');
        $this->dropColumn('sales', 'plane_id');
        $this->dropColumn('sales', 'unshared_fees');
    }
}
