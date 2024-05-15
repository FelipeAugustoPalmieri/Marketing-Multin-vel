<?php

use yii\db\Migration;

/**
 * Handles adding position to table `consumers`.
 */
class m160912_220515_add_position_to_consumers extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('consumers', 'position', 'string');
        $this->execute("
        UPDATE consumers c
        SET position = (CASE t.row % 2
            WHEN 0 THEN 'left'
            ELSE 'right'
        END)
        FROM (
            SELECT row_number() OVER (ORDER BY parent_consumer_id) AS row, id
            FROM consumers
            WHERE parent_consumer_id IS NOT NULL
            ORDER BY parent_consumer_id
        ) AS t
        WHERE t.id = c.id
        ");
        $this->createIndex('idx_consumers_parent', 'consumers', ['parent_consumer_id', 'position'], true);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropIndex('idx_consumers_parent', 'consumers');
        $this->dropColumn('consumers', 'position');
    }
}
