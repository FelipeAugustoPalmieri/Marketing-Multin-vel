<?php

use yii\db\Migration;

class m170315_194015_configuracao_comissao_representante extends Migration
{
    public function up()
    {
        $this->insert('configurations', [
            'name' => 'Percentual de comissão do representante',
            'type' => 'float',
            'value' => '5.0',
        ]);
    }

    public function down()
    {
        $this->delete('configurations', "name = 'Percentual de comissão do representante'");
    }
}
