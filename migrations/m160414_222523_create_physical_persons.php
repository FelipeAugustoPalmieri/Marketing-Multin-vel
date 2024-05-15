<?php

use yii\db\Expression;
use yii\db\Migration;

class m160414_222523_create_physical_persons extends Migration
{
    public function safeUp()
    {
        $this->addColumn('businesses', 'economic_activity', $this->string());
        $this->addColumn('businesses', 'shared_percentage', $this->float());

        $this->execute('UPDATE businesses SET economic_activity = (SELECT pj.economic_activity FROM juridical_persons pj WHERE pj.id = legal_person_id)');
        $this->execute('UPDATE businesses SET shared_percentage = (SELECT pj.shared_percentage FROM juridical_persons pj WHERE pj.id = legal_person_id)');

        $this->dropColumn('juridical_persons', 'economic_activity');
        $this->dropColumn('juridical_persons', 'shared_percentage');

        $this->createTable(
            'physical_persons',
            [
                'id' => $this->primaryKey(),
                'created_at' => $this->timestamp()->notNull(),
                'updated_at' => $this->timestamp(),
                'name' => $this->string()->notNull(),
                'cpf' => $this->string()->notNull()->unique(),
                'website' => $this->string(),
            ]
        );
    }

    public function safeDown()
    {
        $this->addColumn('juridical_persons', 'economic_activity', $this->string());
        $this->addColumn('juridical_persons', 'shared_percentage', $this->float());

        $this->execute('UPDATE juridical_persons pj SET economic_activity = (SELECT economic_activity FROM businesses WHERE legal_person_id = pj.id)');
        $this->execute('UPDATE juridical_persons pj SET shared_percentage = (SELECT shared_percentage FROM businesses WHERE legal_person_id = pj.id)');

        $this->dropColumn('businesses', 'economic_activity');
        $this->dropColumn('businesses', 'shared_percentage');

        $this->dropTable('physical_persons');
    }
}
