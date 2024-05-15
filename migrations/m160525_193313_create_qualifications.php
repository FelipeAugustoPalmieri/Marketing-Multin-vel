<?php

use yii\db\Migration;

/**
 * Handles the creation for table `qualifications`.
 */
class m160525_193313_create_qualifications extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('qualifications', [
            'id' => $this->primaryKey(),
            'description' => $this->string()->notNull()->unique(),
            'gain_percentage' => $this->float(),
            'position' => $this->integer()->notNull()->unique(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('qualifications');
    }
}
