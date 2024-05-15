<?php

use yii\db\Migration;

class m160913_205432_phone_numbers_columns_consumer extends Migration
{
    public function safeUp()
    {
        $this->renameColumn(
            'legal_persons',
            'phone_number',
            'cell_number'
        );

        $this->addColumn(
            'legal_persons',
            'comercial_phone',
            $this->string()
        );

        $this->addColumn(
            'legal_persons',
            'home_phone',
            $this->string()
        );
    }

    public function safeDown()
    {
        $this->renameColumn(
            'legal_persons',
            'cell_number',
            'phone_number'
        );

        $this->dropColumn('comercial_phone');
        $this->dropColumn('home_phone');
    }

}
