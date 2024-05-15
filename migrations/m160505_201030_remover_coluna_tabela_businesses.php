<?php

use yii\db\Migration;

class m160505_201030_remover_coluna_tabela_businesses extends Migration
{
    public function up()
    {
        $this->dropColumn('businesses', 'shared_percentage');
    }

    public function down()
    {
        echo "m160505_201030_remover_coluna_tabela_bussinesses cannot be reverted.\n";

        return false;
    }

}
