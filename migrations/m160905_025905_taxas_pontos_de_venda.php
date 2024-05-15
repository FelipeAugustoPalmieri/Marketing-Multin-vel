<?php

use yii\db\Migration;

class m160905_025905_taxas_pontos_de_venda extends Migration
{
    public function up()
    {
        $this->addColumn('sales', 'points', $this->float());
        $this->addColumn('sales', 'fees', $this->float());
    }

    public function down()
    {
        $this->dropColumn('sales', 'points');
        $this->dropColumn('sales', 'fees');
    }
}
