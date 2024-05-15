<?php

use yii\db\Migration;

class m161017_101337_configuracao_percentual_venda_consumidor extends Migration
{
    public function up()
    {
        $this->insert('configurations', [
            'name' => 'Percentual de repasse para consumidor',
            'type' => 'float',
            'value' => '11.0',
        ]);
    }

    public function down()
    {
        $this->delete('configurations', "name = 'Percentual de repasse para consumidor'");
    }
}
