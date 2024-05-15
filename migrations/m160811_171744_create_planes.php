<?php

use yii\db\Migration;

/**
 * Handles the creation for table `planes`.
 */
class m160811_171744_create_planes extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('planes', [
            'id' => $this->primaryKey(),
            'name_plane' => $this->string()->notNull()->unique(),
            'multiplier' => $this->float()->notNull(),
            'goal_points' => $this->float()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('planes');
    }
}
