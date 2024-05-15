<?php

use yii\db\Migration;

/**
 * Handles adding sponsor_consumer_id to table `consumers`.
 */
class m160505_125412_add_sponsor_consumer_id_to_consumers extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('consumers', 'sponsor_consumer_id', $this->integer());
        $this->addForeignKey(
            'fk-consumers-sponsor_consumer_id',
            'consumers',
            'sponsor_consumer_id',
            'consumers',
            'id'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
    }
}
