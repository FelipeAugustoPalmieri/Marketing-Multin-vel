<?php

use yii\db\Migration;

class m170814_180527_configuration_id_business_onix extends Migration
{
    public function up()
    {
        $this->insert('configurations', [
            'name' => 'ID Convênio Onix',
            'type' => 'integer',
            'value' => '1',
        ]);
    }

    public function down()
    {
        $this->delete('configurations', "name = 'ID Convênio Onix'");
    }
}
