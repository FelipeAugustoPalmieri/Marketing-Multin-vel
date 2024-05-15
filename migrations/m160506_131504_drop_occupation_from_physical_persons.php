<?php

use yii\db\Migration;

/**
 * Handles dropping occupation from table `physical_persons`.
 */
class m160506_131504_drop_occupation_from_physical_persons extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropColumn('physical_persons', 'occupation');
        $this->addColumn('physical_persons', 'occupation_id', $this->integer());
        $this->addForeignKey(
            'fk-physical_persons-occupation_id',
            'physical_persons',
            'occupation_id',
            'occupations',
            'id'
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "Cannot migrate m160506_131504_drop_occupation_from_physical_persons down";
        return false;
    }
}
