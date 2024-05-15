<?php

use yii\db\Migration;

class m161016_184214_quanification_register_network_sale extends Migration
{
    public function up()
    {
        $this->addColumn('qualifications', 'register_network_sale', $this->boolean());
    }

    public function down()
    {
        $this->dropColumn('qualifications', 'register_network_sale');
    }
}
